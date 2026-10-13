<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOrderRequest;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    private const MAX_MONEY_CENTS = 9_999_999_999;

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', 'in:pending,processing,shipped,completed,cancelled'],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $orders = Order::query()
            ->with('customer:id,name')
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('date', '<=', $date))
            ->latest('date')
            ->paginate(10)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('admin.orders.create', [
            'customers' => Customer::query()->orderBy('name')->get(['id', 'name', 'email']),
            'products' => Product::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'sku', 'price', 'stock']),
        ]);
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $order = DB::transaction(function () use ($data): Order {
            $requestedItems = collect($data['items'])->sortBy('product_id');
            $products = Product::query()
                ->whereIn('id', $requestedItems->pluck('product_id'))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');
            $lineTotals = [];
            $totalCents = 0;

            foreach ($requestedItems as $index => $item) {
                $product = $products->get($item['product_id']);

                if (! $product || ! $product->is_active) {
                    throw ValidationException::withMessages([
                        "items.$index.product_id" => 'This product is no longer available.',
                    ]);
                }

                if ($product->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        "items.$index.quantity" => "Only {$product->stock} units of {$product->name} are currently in stock.",
                    ]);
                }

                $unitPriceCents = (int) round((float) $product->price * 100);
                $lineTotalCents = $unitPriceCents * (int) $item['quantity'];

                if ($lineTotalCents > self::MAX_MONEY_CENTS) {
                    throw ValidationException::withMessages([
                        "items.$index.quantity" => 'This line total exceeds the maximum supported order amount.',
                    ]);
                }

                $totalCents += $lineTotalCents;

                if ($totalCents > self::MAX_MONEY_CENTS) {
                    throw ValidationException::withMessages([
                        "items.$index.quantity" => 'The order total exceeds the maximum supported amount.',
                    ]);
                }

                $lineTotals[$index] = $lineTotalCents;
            }

            $order = Order::create([
                'customer_id' => $data['customer_id'],
                'date' => now()->toDateString(),
                'status' => 'pending',
                'total' => '0.00',
            ]);
            foreach ($requestedItems as $index => $item) {
                $product = $products->get($item['product_id']);
                $quantity = (int) $item['quantity'];
                $lineTotalCents = $lineTotals[$index];

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'line_total' => self::formatCents($lineTotalCents),
                ]);
                $product->decrement('stock', $quantity);
            }

            $order->update(['total' => self::formatCents($totalCents)]);

            return $order;
        });

        return redirect()->route('admin.orders.show', $order)
            ->with('success', "Order {$order->order_number} created successfully.");
    }

    public function show(Order $order): View
    {
        $order->load([
            'customer:id,name,email,phone,city',
            'items.product:id,name,sku',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $status = $request->validated('status');

        DB::transaction(function () use ($order, $status): void {
            $lockedOrder = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($lockedOrder->status === 'cancelled' && $status !== 'cancelled') {
                throw ValidationException::withMessages([
                    'status' => 'A cancelled order cannot be reopened.',
                ]);
            }

            if ($status === 'cancelled' && $lockedOrder->status !== 'cancelled') {
                $items = $lockedOrder->items()->orderBy('product_id')->get();
                $products = Product::query()
                    ->whereIn('id', $items->pluck('product_id'))
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($items as $item) {
                    $products->get($item->product_id)->increment('stock', $item->quantity);
                }
            }

            $lockedOrder->update(['status' => $status]);
        });

        return redirect()->route('admin.orders.show', $order)
            ->with('success', "Order {$order->order_number} status updated successfully.");
    }

    private static function formatCents(int $amount): string
    {
        return intdiv($amount, 100).'.'.str_pad((string) ($amount % 100), 2, '0', STR_PAD_LEFT);
    }
}
