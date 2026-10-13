<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'status' => fake()->randomElement(['pending', 'processing', 'shipped', 'completed', 'cancelled']),
            'total' => 0,
            'date' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
