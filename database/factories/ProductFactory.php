<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
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
            'sku' => strtoupper(fake()->unique()->bothify('???-####')),
            'name' => fake()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'unit' => fake()->randomElement(['kos', 'ura', 'dan', 'm2', 'kg', 'pavsal']),
            'price' => fake()->randomFloat(2, 10, 1000),
            'vat_rate' => fake()->randomElement(['22', '9.5', '5', '0']),
            'is_active' => true,
        ];
    }
}
