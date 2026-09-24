<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['legal', 'person']);
        $isLegal = $type === 'legal';

        return [
            'type' => $type,
            'name' => $isLegal ? fake()->company() : fake()->name(),
            'vat_id' => $isLegal ? 'SI' . fake()->numerify('########') : null,
            'is_vat_payer' => $isLegal,
            'address' => fake()->streetAddress(),
            'postal_code' => fake()->postcode(),
            'city' => fake()->city(),
            'country' => 'SI',
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
