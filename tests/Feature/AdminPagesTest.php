<?php

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

it('requires authentication to view admin pages', function (string $url) {
    $this->get($url)
        ->assertRedirect(route('login'));
})->with([
    'dashboard' => '/admin/dashboard',
    'categories' => '/admin/categories',
    'products' => '/admin/products',
    'customers' => '/admin/customers',
    'orders' => '/admin/orders',
    'reports' => '/admin/reports',
]);

it('renders the admin pages with their database data', function () {
    $category = Category::factory()->create(['name' => 'Office supplies']);
    $customer = Customer::factory()->create([
        'name' => 'Sam Customer',
        'email' => 'sam@example.com',
        'phone' => '555-0134',
        'city' => 'Springfield',
    ]);
    Product::factory()
        ->for($category)
        ->create([
            'name' => 'Notebook',
            'description' => 'Ruled paper notebook',
            'price' => 12.50,
            'stock' => 8,
        ]);
    $order = Order::factory()
        ->for($customer)
        ->create([
            'status' => 'completed',
            'total' => 25.00,
            'date' => '2026-10-01',
        ]);
    Order::factory()
        ->for($customer)
        ->create([
            'status' => 'cancelled',
            'total' => 100.00,
            'date' => '2026-10-02',
        ]);

    $this->actingAs(User::factory()->create());

    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('css/app.css')
        ->assertSeeTextInOrder(['Total products', '1', 'Latest orders'])
        ->assertSeeText('01 Oct 2026');

    $this->get(route('admin.categories.index'))
        ->assertOk()
        ->assertSeeText('Office supplies')
        ->assertSeeText('1');

    $this->get(route('admin.products.index'))
        ->assertOk()
        ->assertSeeText('Notebook')
        ->assertSeeText('Office supplies')
        ->assertSeeText('12.50');

    $this->get(route('admin.customers.index'))
        ->assertOk()
        ->assertSeeText('Sam Customer')
        ->assertSeeText('sam@example.com')
        ->assertSeeText('Springfield');

    $this->get(route('admin.orders.index'))
        ->assertOk()
        ->assertSeeText($order->order_number)
        ->assertSeeText('Sam Customer')
        ->assertSeeText('01 Oct 2026');

    $this->get(route('admin.reports.index'))
        ->assertOk()
        ->assertSeeText('1. Total sales and orders per category')
        ->assertSeeText('2. Top 5 customers by total spent')
        ->assertSeeText('3. Customers who have never placed an order');
});
