<?php

namespace Database\Factories;

use App\Models\Quote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quote>
 */
class QuoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $issuedAt = $this->faker->dateTimeBetween('-6 months', 'now');
        $validUntil = (clone $issuedAt)->modify('+30 days');

        return [
            'customer_id' => \App\Models\Customer::factory(),
            'contact_id' => null, // Will be set by relationship
            'user_id' => \App\Models\User::factory(),
            'issued_at' => $issuedAt,
            'valid_until' => $validUntil,
            'status' => $this->faker->randomElement(\App\Enums\QuoteStatus::cases()),
            'notes' => $this->faker->optional()->sentence(),
            'terms' => $this->faker->optional()->paragraph(),
            'subtotal' => 0, // Will be calculated
            'discount_total' => 0,
            'vat_total' => 0,
            'total' => 0,
            'sent_at' => null,
            'accepted_at' => null,
        ];
    }
}
