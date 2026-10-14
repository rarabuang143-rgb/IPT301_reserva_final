<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<\App\Models\Delivery> */
class DeliveryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'courier_name' => fake()->name(),
            'status' => fake()->randomElement(\App\Models\Delivery::STATUSES),
            'dispatched_at' => null,
            'delivered_at' => null,
        ];
    }
}
