<?php

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;

it('renders shared create and edit forms for each resource', function () {
    $this->actingAs(User::factory()->create());
    $category = Category::factory()->create(['name' => 'Books']);
    $product = Product::factory()->for($category)->create(['name' => 'Reading lamp']);
    $customer = Customer::factory()->create(['name' => 'Taylor Customer']);

    $this->get(route('admin.categories.create'))
        ->assertOk()
        ->assertSee(route('admin.categories.store'), false)
        ->assertSee('name="_token"', false);
    $this->get(route('admin.categories.edit', $category))
        ->assertOk()
        ->assertSee(route('admin.categories.update', $category), false)
        ->assertSee('name="_method" value="PUT"', false)
        ->assertSee('value="Books"', false);

    $this->get(route('admin.products.create'))
        ->assertOk()
        ->assertSee('Select a category')
        ->assertSee('Books');
    $this->get(route('admin.products.edit', $product))
        ->assertOk()
        ->assertSee(route('admin.products.update', $product), false)
        ->assertSee('name="_method" value="PUT"', false)
        ->assertSee('value="Reading lamp"', false);

    $this->get(route('admin.customers.create'))
        ->assertOk()
        ->assertSee(route('admin.customers.store'), false);
    $this->get(route('admin.customers.edit', $customer))
        ->assertOk()
        ->assertSee(route('admin.customers.update', $customer), false)
        ->assertSee('name="_method" value="PUT"', false)
        ->assertSee('value="Taylor Customer"', false);
});

it('creates and updates categories while validating unique names', function () {
    $this->actingAs(User::factory()->create());
    $category = Category::factory()->create(['name' => 'Stationery']);

    $this->put(route('admin.categories.update', $category), [
        'name' => 'Stationery',
    ])
        ->assertRedirect(route('admin.categories.index'))
        ->assertSessionHas('success', 'Category updated successfully.');

    $this->from(route('admin.categories.create'))
        ->post(route('admin.categories.store'), ['name' => 'Stationery'])
        ->assertRedirect(route('admin.categories.create'))
        ->assertSessionHasErrors('name')
        ->assertSessionHas('_old_input', fn (array $input): bool => $input['name'] === 'Stationery');

    $this->get(route('admin.categories.create'))
        ->assertOk()
        ->assertSee('value="Stationery"', false)
        ->assertSeeText('The name has already been taken.');

    $this->post(route('admin.categories.store'), ['name' => 'Office'])
        ->assertRedirect(route('admin.categories.index'))
        ->assertSessionHas('success', 'Category created successfully.');

    $this->assertDatabaseHas('categories', ['name' => 'Office']);
});

it('searches and paginates categories and counts their products', function () {
    $this->actingAs(User::factory()->create());
    $matchingCategory = Category::factory()->create(['name' => 'Kitchen tools']);
    Product::factory()->count(2)->for($matchingCategory)->create();
    Category::factory()->count(11)->create();

    $this->get(route('admin.categories.index', ['search' => 'Kitchen']))
        ->assertOk()
        ->assertSeeText('Kitchen tools')
        ->assertSeeText('2')
        ->assertDontSeeText('No categories yet');

    $pageTwo = $this->get(route('admin.categories.index', ['page' => 2]));

    $pageTwo->assertOk();
    $this->assertSame(2, $pageTwo->viewData('categories')->currentPage());
});

it('blocks category deletion while products exist and deletes empty categories', function () {
    $this->actingAs(User::factory()->create());
    $categoryWithProducts = Category::factory()->create();
    Product::factory()->for($categoryWithProducts)->create();
    $emptyCategory = Category::factory()->create();

    $this->delete(route('admin.categories.destroy', $categoryWithProducts))
        ->assertRedirect(route('admin.categories.index'))
        ->assertSessionHas('error', 'This category cannot be deleted while it has products.');

    $this->assertModelExists($categoryWithProducts);

    $this->delete(route('admin.categories.destroy', $emptyCategory))
        ->assertRedirect(route('admin.categories.index'))
        ->assertSessionHas('success', 'Category deleted successfully.');

    $this->assertDatabaseMissing('categories', ['id' => $emptyCategory->id]);
});

it('creates updates and deletes products', function () {
    $this->actingAs(User::factory()->create());
    $category = Category::factory()->create();

    $this->post(route('admin.products.store'), [
        'name' => 'Travel mug',
        'sku' => 'MUG-001',
        'description' => 'Insulated steel mug',
        'price' => '18.50',
        'stock' => '5',
        'is_active' => '1',
        'category_id' => $category->id,
    ])
        ->assertRedirect(route('admin.products.index'))
        ->assertSessionHas('success', 'Product created successfully.');

    $product = Product::query()->where('name', 'Travel mug')->firstOrFail();

    $this->put(route('admin.products.update', $product), [
        'name' => 'Travel mug large',
        'sku' => 'MUG-002',
        'description' => '',
        'price' => '20.00',
        'stock' => '0',
        'is_active' => '0',
        'category_id' => $category->id,
    ])
        ->assertRedirect(route('admin.products.index'))
        ->assertSessionHas('success', 'Product updated successfully.');

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Travel mug large',
        'sku' => 'MUG-002',
        'stock' => 0,
        'is_active' => false,
    ]);

    $this->delete(route('admin.products.destroy', $product))
        ->assertRedirect(route('admin.products.index'))
        ->assertSessionHas('success', 'Product deleted successfully.');

    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});

it('searches and paginates products by name', function () {
    $this->actingAs(User::factory()->create());
    Product::factory()->create(['name' => 'Blue bicycle']);
    Product::factory()->count(11)->create();

    $response = $this->get(route('admin.products.index', ['search' => 'Blue']));

    $response->assertOk()
        ->assertSeeText('Blue bicycle')
        ->assertDontSeeText('No products yet');

    $this->assertSame(1, $response->viewData('products')->total());
    $this->assertSame(10, $this->get(route('admin.products.index'))->viewData('products')->perPage());
});

it('filters products by name or SKU, category, status, and stock together', function () {
    $this->actingAs(User::factory()->create());
    $category = Category::factory()->create(['name' => 'Garden']);
    Product::factory()
        ->count(12)
        ->for($category)
        ->state([
            'name' => 'Garden gloves',
            'is_active' => true,
            'stock' => 0,
        ])
        ->sequence(...array_map(
            fn (int $index): array => ['sku' => 'GARDEN-'.str_pad((string) $index, 2, '0', STR_PAD_LEFT)],
            range(1, 12),
        ))
        ->create();
    Product::factory()->for($category)->create([
        'name' => 'Garden shears',
        'sku' => 'SHEARS-001',
        'is_active' => false,
        'stock' => 0,
    ]);
    Product::factory()->create([
        'name' => 'Garden gloves',
        'sku' => 'OTHER-CATEGORY',
        'is_active' => true,
        'stock' => 0,
    ]);
    Product::factory()->for($category)->create([
        'name' => 'Garden gloves',
        'sku' => 'IN-STOCK',
        'is_active' => true,
        'stock' => 5,
    ]);

    $filters = [
        'search' => 'GARDEN-',
        'category_id' => $category->id,
        'status' => 'active',
        'out_of_stock' => '1',
    ];

    $response = $this->get(route('admin.products.index', $filters));

    $response->assertOk()
        ->assertSeeText('GARDEN-01')
        ->assertDontSeeText('SHEARS-001')
        ->assertDontSeeText('OTHER-CATEGORY')
        ->assertDontSeeText('IN-STOCK');

    $paginator = $response->viewData('products');
    $this->assertSame(12, $paginator->total());
    $this->assertSame(10, $paginator->perPage());

    parse_str((string) parse_url($paginator->url(2), PHP_URL_QUERY), $pageTwoQuery);
    $this->assertSame($filters['search'], $pageTwoQuery['search']);
    $this->assertSame((string) $category->id, $pageTwoQuery['category_id']);
    $this->assertSame('active', $pageTwoQuery['status']);
    $this->assertSame('1', $pageTwoQuery['out_of_stock']);
});

it('validates SKU uniqueness and positive product prices', function () {
    $this->actingAs(User::factory()->create());
    $category = Category::factory()->create();
    $inactiveCategory = Category::factory()->create(['is_active' => false]);
    $existingProduct = Product::factory()->for($category)->create([
        'sku' => 'UNIQUE-001',
    ]);

    $this->from(route('admin.products.create'))
        ->post(route('admin.products.store'), [
            'name' => 'Duplicate SKU item',
            'sku' => 'UNIQUE-001',
            'price' => '0',
            'stock' => '0',
            'is_active' => '1',
            'category_id' => $category->id,
        ])
        ->assertRedirect(route('admin.products.create'))
        ->assertSessionHasErrors(['sku', 'price']);

    $this->from(route('admin.products.create'))
        ->post(route('admin.products.store'), [
            'name' => 'Invalid stock product',
            'sku' => 'UNIQUE-002',
            'price' => '15.00',
            'stock' => '1.5',
            'is_active' => '1',
            'category_id' => $inactiveCategory->id,
        ])
        ->assertRedirect(route('admin.products.create'))
        ->assertSessionHasErrors(['stock', 'category_id']);

    $this->put(route('admin.products.update', $existingProduct), [
        'name' => $existingProduct->name,
        'sku' => 'UNIQUE-001',
        'price' => '15.00',
        'stock' => '0',
        'is_active' => '1',
        'category_id' => $category->id,
    ])
        ->assertRedirect(route('admin.products.index'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('products', [
        'id' => $existingProduct->id,
        'sku' => 'UNIQUE-001',
    ]);
});

it('only offers active categories for new products and keeps an existing inactive category on edit', function () {
    $this->actingAs(User::factory()->create());
    $activeCategory = Category::factory()->create(['name' => 'Available', 'is_active' => true]);
    $inactiveCategory = Category::factory()->create(['name' => 'Archived', 'is_active' => false]);
    $product = Product::factory()->for($inactiveCategory)->create();

    $this->get(route('admin.products.create'))
        ->assertOk()
        ->assertSeeText('Available')
        ->assertDontSeeText('Archived')
        ->assertSee('name="sku"', false)
        ->assertSee('min="0.01"', false);

    $this->get(route('admin.products.edit', $product))
        ->assertOk()
        ->assertSeeText('Available')
        ->assertSeeText('Archived (inactive - current category)');

    $this->put(route('admin.products.update', $product), [
        'name' => $product->name,
        'sku' => $product->sku,
        'price' => $product->price,
        'stock' => $product->stock,
        'is_active' => '1',
        'category_id' => $inactiveCategory->id,
    ])->assertRedirect(route('admin.products.index'));

    $this->put(route('admin.products.update', $product), [
        'name' => $product->name,
        'sku' => $product->sku,
        'price' => $product->price,
        'stock' => $product->stock,
        'is_active' => '1',
        'category_id' => $activeCategory->id,
    ])->assertRedirect(route('admin.products.index'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'category_id' => $activeCategory->id,
    ]);
});

it('eager loads product categories with a fixed number of list queries', function () {
    $this->actingAs(User::factory()->create());
    Product::factory()->count(12)->create();
    $productQueries = 0;
    $categoryQueries = 0;

    DB::listen(function (QueryExecuted $query) use (&$productQueries, &$categoryQueries): void {
        if (str_starts_with(strtolower($query->sql), 'select') && str_contains($query->sql, 'products')) {
            $productQueries++;
        }

        if (str_starts_with(strtolower($query->sql), 'select') && str_contains($query->sql, 'categories')) {
            $categoryQueries++;
        }
    });

    $this->get(route('admin.products.index'))->assertOk();

    $this->assertSame(2, $productQueries);
    $this->assertSame(2, $categoryQueries);
});

it('creates and updates customers without rejecting their unchanged email', function () {
    $this->actingAs(User::factory()->create());
    $customer = Customer::factory()->create([
        'name' => 'Alex Smith',
        'email' => 'alex@example.com',
    ]);

    $this->put(route('admin.customers.update', $customer), [
        'name' => 'Alex Smith Updated',
        'email' => 'alex@example.com',
        'phone' => '',
        'city' => 'Boston',
    ])
        ->assertRedirect(route('admin.customers.index'))
        ->assertSessionHas('success', 'Customer updated successfully.');

    $this->from(route('admin.customers.create'))
        ->post(route('admin.customers.store'), [
            'name' => 'Duplicate Email',
            'email' => 'alex@example.com',
        ])
        ->assertRedirect(route('admin.customers.create'))
        ->assertSessionHasErrors('email');

    $this->post(route('admin.customers.store'), [
        'name' => 'Jamie Customer',
        'email' => 'jamie@example.com',
        'phone' => '555-0100',
        'city' => 'Seattle',
    ])
        ->assertRedirect(route('admin.customers.index'))
        ->assertSessionHas('success', 'Customer created successfully.');

    $this->assertDatabaseHas('customers', ['email' => 'jamie@example.com']);
});

it('searches customers by name or email and shows their orders', function () {
    $this->actingAs(User::factory()->create());
    $customer = Customer::factory()->create([
        'name' => 'Morgan Customer',
        'email' => 'morgan@example.com',
    ]);
    Order::factory()->for($customer)->create([
        'status' => 'completed',
        'total' => 42.50,
        'date' => '2026-10-01',
    ]);
    Customer::factory()->count(11)->create();

    $this->get(route('admin.customers.index', ['search' => 'morgan@example']))
        ->assertOk()
        ->assertSeeText('Morgan Customer')
        ->assertSeeText('morgan@example.com');

    $this->get(route('admin.customers.show', $customer))
        ->assertOk()
        ->assertSeeText('Morgan Customer')
        ->assertSeeText('completed')
        ->assertSeeText('42.50');

    $this->assertSame(10, $this->get(route('admin.customers.index'))->viewData('customers')->perPage());
});

it('blocks customer deletion while orders exist and deletes customers without orders', function () {
    $this->actingAs(User::factory()->create());
    $customerWithOrders = Customer::factory()->create();
    Order::factory()->for($customerWithOrders)->create();
    $customerWithoutOrders = Customer::factory()->create();

    $this->delete(route('admin.customers.destroy', $customerWithOrders))
        ->assertRedirect(route('admin.customers.index'))
        ->assertSessionHas('error', 'This customer cannot be deleted while they have orders.');

    $this->assertModelExists($customerWithOrders);

    $this->delete(route('admin.customers.destroy', $customerWithoutOrders))
        ->assertRedirect(route('admin.customers.index'))
        ->assertSessionHas('success', 'Customer deleted successfully.');

    $this->assertDatabaseMissing('customers', ['id' => $customerWithoutOrders->id]);
});
