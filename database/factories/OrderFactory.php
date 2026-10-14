<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Food;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<\App\Models\Order> */
class OrderFactory extends Factory
{
    public function configure(): static
    {
        return $this->afterCreating(function (Order $order): void {
            $order->update(['total' => $order->food->price * $order->quantity]);
        });
    }

    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 4);

        return [
            'customer_id' => Customer::factory(),
            'food_id' => Food::factory(),
            'quantity' => $quantity,
            'total' => fake()->randomFloat(2, 8, 120),
            'status' => fake()->randomElement(\App\Models\Order::STATUSES),
        ];
    }
}
