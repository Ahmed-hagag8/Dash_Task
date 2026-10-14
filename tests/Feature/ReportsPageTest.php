<?php

use App\Services\ReportService;
use Illuminate\Support\Facades\DB;

it('matches the dashboard stats to the seeded data', function () {
    $this->seed();

    $reportService = app(ReportService::class);

    $stats = $reportService->dashboardStats();

    expect($stats['total_products'])->toBe(40)
        ->and($stats['out_of_stock_products'])->toBe(5)
        ->and($stats['orders_today'])->toBe((int) DB::table('orders')->whereDate('date', today())->count())
        ->and($stats['sales_this_month'])->toBe((float) DB::table('order_items')
        ->join('orders', 'orders.id', '=', 'order_items.order_id')
        ->where('orders.status', '!=', 'cancelled')
        ->whereMonth('orders.date', now()->month)
        ->whereYear('orders.date', now()->year)
        ->sum('order_items.line_total'));
});

it('matches the report queries in eloquent and raw SQL on the seeded data', function () {
    $this->seed();

    $reportService = app(ReportService::class);

    $categorySales = $reportService->categorySales()
        ->map(fn ($row) => [
            'category_name' => $row->category_name,
            'orders_count' => (int) $row->orders_count,
            'total_sales' => (float) $row->total_sales,
        ])
        ->values()
        ->all();
    $expectedCategorySales = DB::select(<<<'SQL'
        SELECT
            c.name AS category_name,
            COUNT(DISTINCT o.id) AS orders_count,
            SUM(oi.line_total) AS total_sales
        FROM categories c
        JOIN products p ON p.category_id = c.id
        JOIN order_items oi ON oi.product_id = p.id
        JOIN orders o ON o.id = oi.order_id
        WHERE o.status != 'cancelled'
        GROUP BY c.id, c.name
        ORDER BY total_sales DESC
    SQL);
    foreach ($categorySales as $index => $row) {
        $expected = $expectedCategorySales[$index];

        expect($row['category_name'])->toBe($expected->category_name)
            ->and($row['orders_count'])->toBe((int) $expected->orders_count)
            ->and(abs((float) $row['total_sales'] - (float) $expected->total_sales))->toBeLessThanOrEqual(0.01);
    }

    $topCustomers = $reportService->topCustomers()
        ->map(fn ($row) => [
            'customer_name' => $row->customer_name,
            'customer_city' => $row->customer_city,
            'order_count' => (int) $row->order_count,
            'total_spent' => (float) $row->total_spent,
        ])
        ->values()
        ->all();
    $expectedTopCustomers = DB::select(<<<'SQL'
        SELECT
            cu.name AS customer_name,
            cu.city AS customer_city,
            COUNT(DISTINCT o.id) AS order_count,
            SUM(oi.line_total) AS total_spent
        FROM customers cu
        JOIN orders o ON o.customer_id = cu.id
        JOIN order_items oi ON oi.order_id = o.id
        WHERE o.status != 'cancelled'
        GROUP BY cu.id, cu.name, cu.city
        ORDER BY total_spent DESC
        LIMIT 5
    SQL);
    foreach ($topCustomers as $index => $row) {
        $expected = $expectedTopCustomers[$index];

        expect($row['customer_name'])->toBe($expected->customer_name)
            ->and($row['customer_city'])->toBe($expected->customer_city)
            ->and($row['order_count'])->toBe((int) $expected->order_count)
            ->and(abs((float) $row['total_spent'] - (float) $expected->total_spent))->toBeLessThanOrEqual(0.01);
    }

    $customersWithoutOrders = $reportService->customersWithoutOrders()
        ->map(fn ($row) => [
            'id' => $row->id,
            'name' => $row->name,
            'email' => $row->email,
            'city' => $row->city,
        ])
        ->values()
        ->all();
    $expectedCustomersWithoutOrders = DB::select(<<<'SQL'
        SELECT
            cu.id,
            cu.name,
            cu.email,
            cu.city
        FROM customers cu
        LEFT JOIN orders o
            ON o.customer_id = cu.id
           AND o.status != 'cancelled'
        WHERE o.id IS NULL
        ORDER BY cu.name
    SQL);
    foreach ($customersWithoutOrders as $index => $row) {
        $expected = $expectedCustomersWithoutOrders[$index];

        expect($row['id'])->toBe($expected->id)
            ->and($row['name'])->toBe($expected->name)
            ->and($row['email'])->toBe($expected->email)
            ->and($row['city'])->toBe($expected->city);
    }

    $productsWithoutOrders = $reportService->productsWithoutOrders()
        ->map(fn ($row) => [
            'product_name' => $row->product_name,
            'total_quantity_sold' => (float) $row->total_quantity_sold,
        ])
        ->values()
        ->all();
    $expectedProductsWithoutOrders = DB::select(<<<'SQL'
        SELECT
            p.name AS product_name,
            COALESCE(SUM(oi.quantity), 0) AS total_quantity_sold
        FROM products p
        LEFT JOIN order_items oi ON oi.product_id = p.id
        LEFT JOIN orders o ON o.id = oi.order_id AND o.status != 'cancelled'
        GROUP BY p.id, p.name
        ORDER BY total_quantity_sold ASC
    SQL);
    foreach ($productsWithoutOrders as $index => $row) {
        $expected = $expectedProductsWithoutOrders[$index];

        expect($row['product_name'])->toBe($expected->product_name)
            ->and(abs((float) $row['total_quantity_sold'] - (float) $expected->total_quantity_sold))->toBeLessThanOrEqual(0.01);
    }

    $monthlySales = $reportService->monthlySalesCurrentYear()
        ->map(fn ($row) => [
            'month_number' => (int) $row['month_number'],
            'month_name' => $row['month_name'],
            'total_sales' => (float) $row['total_sales'],
        ])
        ->values()
        ->all();
    $monthlySelects = [];
    $monthlyBindings = [];

    foreach (range(1, 12) as $month) {
        $monthStart = now()->startOfYear()->addMonths($month - 1);
        $monthlySelects[] = 'COALESCE(SUM(CASE WHEN orders.date >= ? AND orders.date < ? THEN order_items.line_total ELSE 0 END), 0) AS month_'.$month;
        $monthlyBindings[] = $monthStart->toDateString();
        $monthlyBindings[] = $monthStart->copy()->addMonth()->toDateString();
    }

    $yearStart = now()->startOfYear()->toDateString();
    $nextYearStart = now()->startOfYear()->addYear()->toDateString();
    $expectedMonthlySales = DB::select(
        'SELECT '.implode(', ', $monthlySelects).' FROM order_items JOIN orders ON orders.id = order_items.order_id WHERE orders.status != ? AND orders.date >= ? AND orders.date < ?',
        [...$monthlyBindings, 'cancelled', $yearStart, $nextYearStart],
    )[0];

    expect($monthlySales)->toHaveCount(12);

    foreach ($monthlySales as $row) {
        $expectedTotal = $expectedMonthlySales->{'month_'.$row['month_number']};

        expect($row['month_name'])->toBe(now()->setMonth($row['month_number'])->translatedFormat('F'))
            ->and(abs((float) $row['total_sales'] - (float) $expectedTotal))->toBeLessThanOrEqual(0.01);
    }

    $categoriesAboveThreshold = $reportService->categoriesAboveThreshold()
        ->map(fn ($row) => [
            'category_name' => $row->category_name,
            'total_sales' => (float) $row->total_sales,
        ])
        ->values()
        ->all();
    $expectedCategoriesAboveThreshold = DB::select(<<<'SQL'
        SELECT
            c.name AS category_name,
            SUM(oi.line_total) AS total_sales
        FROM categories c
        JOIN products p ON p.category_id = c.id
        JOIN order_items oi ON oi.product_id = p.id
        JOIN orders o ON o.id = oi.order_id
        WHERE o.status != 'cancelled'
        GROUP BY c.id, c.name
        HAVING SUM(oi.line_total) > 10000
        ORDER BY total_sales DESC
    SQL);
    foreach ($categoriesAboveThreshold as $index => $row) {
        $expected = $expectedCategoriesAboveThreshold[$index];

        expect($row['category_name'])->toBe($expected->category_name)
            ->and(abs((float) $row['total_sales'] - (float) $expected->total_sales))->toBeLessThanOrEqual(0.01);
    }
});
