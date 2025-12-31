<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ucfirst(fake()->words(2, true)),
            'description' => fake()->sentence(),
            'price' => fake()->numberBetween(50000, 5000000), // Harga 50rb - 5jt
            'stock' => fake()->numberBetween(10, 100),
            'is_active' => true,
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
