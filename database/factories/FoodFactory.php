<?php

namespace Database\Factories;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<\App\Models\Food> */
class FoodFactory extends Factory
{
    public function definition(): array
    {
        return [
            'restaurant_id' => Restaurant::factory(),
            'name' => fake()->randomElement(['Chicken Adobo', 'Margherita Pizza', 'Ramen Bowl', 'Beef Tacos', 'Pad Thai']),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 5, 40),
            'available' => true,
        ];
    }
}
