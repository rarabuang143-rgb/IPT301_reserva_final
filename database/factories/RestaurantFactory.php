<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<\App\Models\Restaurant> */
class RestaurantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company().' Kitchen',
            'cuisine' => fake()->randomElement(['Filipino', 'Italian', 'Japanese', 'Mexican', 'Thai']),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
        ];
    }
}
