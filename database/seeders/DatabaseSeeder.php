<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Food;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $restaurants = Restaurant::factory(5)->create();
        $foods = $restaurants->flatMap(fn (Restaurant $restaurant) => Food::factory(4)->for($restaurant)->create());
        $customers = Customer::factory(15)->create();

        $orders = collect(range(1, 20))->map(function () use ($customers, $foods) {
            $food = $foods->random();
            $quantity = fake()->numberBetween(1, 3);

            return Order::create([
                'customer_id' => $customers->random()->id,
                'food_id' => $food->id,
                'quantity' => $quantity,
                'total' => $food->price * $quantity,
                'status' => fake()->randomElement(Order::STATUSES),
            ]);
        });

        $orders->take(12)->each(fn (Order $order) => Delivery::factory()->for($order)->create());
    }
}
