<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ------------------------------------------------------------------
        // 1) Categories — exactly 6
        // ------------------------------------------------------------------
        $categories = Category::factory()
            ->sequence(
                ['name' => 'Electronics'],
                ['name' => 'Books'],
                ['name' => 'Clothing'],
                ['name' => 'Home & Garden'],
                ['name' => 'Sports'],
                ['name' => 'Toys'],
            )
            ->count(6)
            ->create();

        Product::factory()->count(30)->recycle($categories)->create();
        Product::factory()->count(5)->recycle($categories)->outOfStock()->create();
        Product::factory()->count(5)->recycle($categories)->inactive()->create();

        $orderable = Product::where('is_active', true)->get()
            ->shuffle()
            ->slice(3)
            ->values();

        // ------------------------------------------------------------------
        // 3) Customers — exactly 25, of which 5 never place an order
        // ------------------------------------------------------------------
        $customers = Customer::factory()->count(25)->create()->shuffle();

        $buyers = $customers->take(20)->values();
        // The 5 customers left behind ($customers->slice(20)) get zero orders.

        // ------------------------------------------------------------------
        // 4) Orders — exactly 40, all 5 statuses, 1–4 items each,
        //    total = SUM(line_total)
        // ------------------------------------------------------------------
        $statuses = collect([
            ...array_fill(0, 10, 'completed'),   // 10
            ...array_fill(0, 8, 'shipped'),     //  8
            ...array_fill(0, 8, 'pending'),     //  8
            ...array_fill(0, 7, 'processing'),  //  7
            ...array_fill(0, 7, 'cancelled'),   //  7  -> 40 total
        ])->shuffle();

        foreach ($statuses as $i => $status) {
            $order = Order::factory()
                ->for($buyers[$i % $buyers->count()])
                ->create([
                    'status' => $status,
                    'date' => match ($status) {
                        'pending' => fake()->dateTimeBetween('-7 days', 'now'),
                        'processing' => fake()->dateTimeBetween('-14 days', '-7 days'),
                        'shipped' => fake()->dateTimeBetween('-1 month', '-7 days'),
                        'cancelled' => fake()->dateTimeBetween('-3 months', 'now'),
                        default => fake()->dateTimeBetween('-6 months', '-1 month'), // completed
                    },
                ]);

            // 1 to 4 distinct products per order
            foreach ($orderable->random(rand(1, 4)) as $product) {
                $quantity = fake()->numberBetween(1, 3);

                OrderItem::factory()
                    ->for($order)
                    ->for($product)
                    ->create([
                        'quantity' => $quantity,
                        'unit_price' => $product->price,
                    ]);
            }

        }
    }
}
