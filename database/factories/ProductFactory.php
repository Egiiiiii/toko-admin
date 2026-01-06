<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Product>
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
            'name'        => ucfirst($this->faker->words(2, true)),
            'description' => $this->faker->sentence(),
            'price'       => $this->faker->numberBetween(50_000, 5_000_000),
            'stock'       => $this->faker->numberBetween(10, 100),
            'is_active'   => true,
            'created_at'  => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
