<?php

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

it('requires authentication to manage orders', function () {
    $this->get(route('admin.orders.create'))
        ->assertRedirect(route('login'));

    $this->post(route('admin.orders.store'), [])
        ->assertRedirect(route('login'));
});

it('filters orders by status and date range while preserving filters in pagination', function () {
    $this->actingAs(User::factory()->create());
    $customer = Customer::factory()->create();
    Order::factory()
        ->count(12)
        ->for($customer)
        ->state([
            'status' => 'processing',
            'date' => '2026-10-01',
        ])
        ->create();
    Order::factory()->for($customer)->create([
        'status' => 'cancelled',
        'date' => '2026-10-02',
    ]);
    Order::factory()->for($customer)->create([
        'status' => 'processing',
        'date' => '2026-09-30',
    ]);

    $filters = [
        'status' => 'processing',
        'date_from' => '2026-10-01',
        'date_to' => '2026-10-03',
    ];
    $response = $this->get(route('admin.orders.index', $filters));

    $response->assertOk()
        ->assertSeeText('ORD-');

    $orders = $response->viewData('orders');
    expect($orders->total())->toBe(12)
        ->and($orders->perPage())->toBe(10);

    parse_str((string) parse_url($orders->url(2), PHP_URL_QUERY), $pageTwoQuery);
    expect($pageTwoQuery)->toMatchArray($filters)
        ->and($pageTwoQuery['page'])->toBe('2');
});

it('creates orders using current catalog prices and reduces stock', function () {
    $this->actingAs(User::factory()->create());
    $customer = Customer::factory()->create();
    $category = Category::factory()->create();
    $lamp = Product::factory()->for($category)->create([
        'name' => 'Desk lamp',
        'sku' => 'LAMP-001',
        'price' => '12.50',
        'stock' => 5,
    ]);
    $notebook = Product::factory()->for($category)->create([
        'name' => 'Notebook',
        'sku' => 'NOTE-001',
        'price' => '4.25',
        'stock' => 10,
    ]);

    $this->get(route('admin.orders.create'))
        ->assertOk()
        ->assertSeeText($customer->name)
        ->assertSeeText('Desk lamp')
        ->assertSee('name="items[0][quantity]"', false);

    $response = $this->post(route('admin.orders.store'), [
        'customer_id' => $customer->id,
        'total' => '0.01',
        'items' => [
            ['product_id' => $lamp->id, 'quantity' => 2, 'unit_price' => '0.01'],
            ['product_id' => $notebook->id, 'quantity' => 3, 'unit_price' => '0.01'],
        ],
    ]);

    $order = Order::query()->latest('id')->firstOrFail();
    $response->assertRedirect(route('admin.orders.show', $order))
        ->assertSessionHas('success', "Order {$order->order_number} created successfully.");

    $anotherOrder = Order::factory()->create();

    expect($order->total)->toBe('37.75')
        ->and($order->order_number)->toMatch('/^ORD-\d{5,}$/')
        ->and($anotherOrder->order_number)->not->toBe($order->order_number)
        ->and($order->items()->where('product_id', $lamp->id)->firstOrFail()->unit_price)->toBe('12.50')
        ->and($order->items()->where('product_id', $lamp->id)->firstOrFail()->line_total)->toBe('25.00')
        ->and($order->items()->where('product_id', $notebook->id)->firstOrFail()->line_total)->toBe('12.75');

    $this->assertDatabaseHas('products', ['id' => $lamp->id, 'stock' => 3]);
    $this->assertDatabaseHas('products', ['id' => $notebook->id, 'stock' => 7]);

    $this->get(route('admin.orders.show', $order))
        ->assertOk()
        ->assertSeeText($order->order_number)
        ->assertSeeText($customer->email)
        ->assertSeeText('Desk lamp')
        ->assertSeeText('25.00')
        ->assertSeeText('37.75');
});

it('limits the order form quantity to the selected product stock', function () {
    $this->actingAs(User::factory()->create());
    $category = Category::factory()->create();
    $product = Product::factory()->for($category)->create(['stock' => 5]);

    $this->withSession([
        '_old_input' => [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ],
    ])
        ->get(route('admin.orders.create'))
        ->assertOk()
        ->assertSee('data-stock="5"', false)
        ->assertSee('max="5"', false)
        ->assertSeeText('Available: 5 units.');
});

it('rejects an order with insufficient stock without saving or changing any stock', function () {
    $this->actingAs(User::factory()->create());
    $customer = Customer::factory()->create();
    $category = Category::factory()->create();
    $availableProduct = Product::factory()->for($category)->create(['stock' => 8]);
    $lowStockProduct = Product::factory()->for($category)->create(['stock' => 1, 'name' => 'Last unit']);

    $this->from(route('admin.orders.create'))
        ->post(route('admin.orders.store'), [
            'customer_id' => $customer->id,
            'items' => [
                ['product_id' => $availableProduct->id, 'quantity' => 2],
                ['product_id' => $lowStockProduct->id, 'quantity' => 2],
            ],
        ])
        ->assertRedirect(route('admin.orders.create'))
        ->assertSessionHasErrors('items.1.quantity');

    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseCount('order_items', 0);
    $this->assertDatabaseHas('products', ['id' => $availableProduct->id, 'stock' => 8]);
    $this->assertDatabaseHas('products', ['id' => $lowStockProduct->id, 'stock' => 1]);
});

it('returns stock exactly once when an order is cancelled', function () {
    $this->actingAs(User::factory()->create());
    $category = Category::factory()->create();
    $product = Product::factory()->for($category)->create(['stock' => 6]);
    $order = Order::factory()->create(['status' => 'processing']);
    OrderItem::factory()->for($order)->for($product)->create([
        'quantity' => 4,
        'unit_price' => '10.00',
        'line_total' => '40.00',
    ]);

    $this->patch(route('admin.orders.status', $order), ['status' => 'cancelled'])
        ->assertRedirect(route('admin.orders.show', $order))
        ->assertSessionHas('success', "Order {$order->order_number} status updated successfully.");

    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 10]);
    $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'cancelled']);

    $this->patch(route('admin.orders.status', $order), ['status' => 'cancelled'])
        ->assertRedirect(route('admin.orders.show', $order));

    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 10]);

    $this->from(route('admin.orders.show', $order))
        ->patch(route('admin.orders.status', $order), ['status' => 'processing'])
        ->assertRedirect(route('admin.orders.show', $order))
        ->assertSessionHasErrors('status');

    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 10]);
    $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'cancelled']);
});

it('rejects duplicate products and invalid quantities in an order', function () {
    $this->actingAs(User::factory()->create());
    $customer = Customer::factory()->create();
    $product = Product::factory()->create(['stock' => 5]);

    $this->from(route('admin.orders.create'))
        ->post(route('admin.orders.store'), [
            'customer_id' => $customer->id,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
                ['product_id' => $product->id, 'quantity' => 0],
            ],
        ])
        ->assertRedirect(route('admin.orders.create'))
        ->assertSessionHasErrors(['items.0.product_id', 'items.1.product_id', 'items.1.quantity']);

    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 5]);
});
