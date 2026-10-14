<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    public function dashboardStats(): array
    {
        return [
            'total_products' => Product::query()->count(),
            'out_of_stock_products' => Product::query()->where('stock', '<', 1)->count(),
            'orders_today' => Order::query()->whereDate('date', today())->count(),
            'sales_this_month' => (float) OrderItem::query()
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.status', '!=', 'cancelled')
                ->whereMonth('orders.date', now()->month)
                ->whereYear('orders.date', now()->year)
                ->sum('order_items.line_total') ?: 0,
        ];
    }

    public function recentOrders(): Collection
    {
        return Order::query()
            ->with('customer:id,name')
            ->latest('date')
            ->limit(5)
            ->get();
    }

    public function lowStockProducts(): Collection
    {
        return Product::query()
            ->with('category:id,name')
            ->where('stock', '<', 5)
            ->orderBy('stock')
            ->orderBy('name')
            ->get();
    }

    public function categorySales(): Collection
    {
        return Category::query()
            ->join('products', 'products.category_id', '=', 'categories.id')
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '!=', 'cancelled')
            ->selectRaw('categories.name as category_name')
            ->selectRaw('COUNT(DISTINCT orders.id) as orders_count')
            ->selectRaw('SUM(order_items.line_total) as total_sales')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_sales')
            ->get();
    }

    public function topCustomers(): Collection
    {
        return Customer::query()
            ->join('orders', 'orders.customer_id', '=', 'customers.id')
            ->join('order_items', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->selectRaw('customers.name as customer_name')
            ->selectRaw('customers.city as customer_city')
            ->selectRaw('COUNT(DISTINCT orders.id) as order_count')
            ->selectRaw('SUM(order_items.line_total) as total_spent')
            ->groupBy('customers.id', 'customers.name', 'customers.city')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get();
    }

    public function customersWithoutOrders(): Collection
    {
        return Customer::query()
            ->leftJoin('orders', function ($join): void {
                $join->on('orders.customer_id', '=', 'customers.id')
                    ->where('orders.status', '!=', 'cancelled');
            })
            ->whereNull('orders.id')
            ->select('customers.id', 'customers.name', 'customers.email', 'customers.city')
            ->orderBy('customers.name')
            ->get();
    }

    public function productsWithoutOrders(): Collection
    {
        return Product::query()
            ->leftJoin('order_items', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('orders', function ($join): void {
                $join->on('orders.id', '=', 'order_items.order_id')
                    ->where('orders.status', '!=', 'cancelled');
            })
                        // first attempt syntax error
            // ->selectRaw('products.name as product_name')
            // ->where('(SUM(order_items.quantity)=0) as total_quantity_sold')
            // ->groupBy('products.id', 'products.name')
            // ->orderBy('total_quantity_sold')
            // ->get();
            
            //second attempt empty table
            // ->selectRaw('products.name as product_name')
            // ->selectRaw('SUM(order_items.quantity) as total_quantity_sold')
            // ->groupBy('products.id', 'products.name')
            // ->havingRaw('SUM(order_items.quantity) = 0')
            // ->orderBy('total_quantity_sold')
            // ->get();

            // third attempt working
            ->selectRaw('products.name as product_name')
            ->selectRaw('COALESCE(SUM(order_items.quantity), 0) as total_quantity_sold')
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_quantity_sold')
            ->get();
    }

    public function monthlySalesCurrentYear(): Collection
    {
        $year = now()->year;
        $yearStart = Carbon::create($year, 1, 1)->toDateString();
        $nextYearStart = Carbon::create($year + 1, 1, 1)->toDateString();
        $monthSelects = [];
        $bindings = [];

        foreach (range(1, 12) as $month) {
            $monthStart = Carbon::create($year, $month, 1);
            $monthSelects[] = 'COALESCE(SUM(CASE WHEN orders.date >= ? AND orders.date < ? THEN order_items.line_total ELSE 0 END), 0) as month_'.$month;
            $bindings[] = $monthStart->toDateString();
            $bindings[] = $monthStart->copy()->addMonth()->toDateString();
        }

        $monthlySales = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '!=', 'cancelled')
            ->where('orders.date', '>=', $yearStart)
            ->where('orders.date', '<', $nextYearStart)
            ->selectRaw(implode(', ', $monthSelects), $bindings)
            ->first();

        return collect(range(1, 12))
            ->map(function (int $month) use ($monthlySales, $year): array {
                $totalSales = $monthlySales->{'month_'.$month} ?? 0;

                return [
                    'month_number' => $month,
                    'month_name' => Carbon::create($year, $month, 1)->translatedFormat('F'),
                    'total_sales' => (float) $totalSales,
                ];
            })
            ->values();
    }

    public function categoriesAboveThreshold(int $threshold = 10000): Collection
    {
        return Category::query()
            ->join('products', 'products.category_id', '=', 'categories.id')
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '!=', 'cancelled')
            ->selectRaw('categories.name as category_name')
            ->selectRaw('SUM(order_items.line_total) as total_sales')
            ->groupBy('categories.id', 'categories.name')
            ->havingRaw('SUM(order_items.line_total) > ?', [$threshold])
            ->orderByDesc('total_sales')
            ->get();
    }
}
