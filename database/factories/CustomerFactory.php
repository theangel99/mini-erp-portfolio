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
        $type = $this->faker->randomElement(['legal', 'person']);
        $isLegal = $type === 'legal';

        return [
            'type' => $type,
            'name' => $isLegal ? $this->faker->company() : $this->faker->name(),
            'vat_id' => $isLegal ? 'SI'.$this->faker->numerify('########') : null,
            'is_vat_payer' => $isLegal,
            'address' => $this->faker->streetAddress(),
            'postal_code' => $this->faker->postcode(),
            'city' => $this->faker->city(),
            'country' => 'SI',
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
