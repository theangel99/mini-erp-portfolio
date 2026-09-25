<?php

namespace Database\Factories;

use App\Models\Product;
use Faker\Generator;
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

    /**
     * Get Faker instance.
     */
    protected function faker(): Generator
    {
        return \Faker\Factory::create();
    }

    public function definition(): array
    {
        return [
            'sku' => strtoupper($this->faker()->unique()->bothify('???-####')),
            'name' => $this->faker()->words(3, true),
            'description' => $this->faker()->optional()->sentence(),
            'unit' => $this->faker()->randomElement(['kos', 'ura', 'dan', 'm2', 'kg', 'pavsal']),
            'price' => $this->faker()->randomFloat(2, 10, 1000),
            'vat_rate' => $this->faker()->randomElement(['22', '9.5', '5', '0']),
            'is_active' => true,
        ];
    }
}
