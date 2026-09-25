<?php

namespace Database\Factories;

use App\Enums\QuoteStatus;
use App\Models\Customer;
use App\Models\Quote;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Faker\Generator;
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

    /**
     * Get Faker instance.
     */
    protected function faker(): Generator
    {
        return FakerFactory::create();
    }

    public function definition(): array
    {
        $issuedAt = $this->faker()->dateTimeBetween('-6 months', 'now');
        $validUntil = (clone $issuedAt)->modify('+30 days');

        return [
            'customer_id' => Customer::factory(),
            'contact_id' => null, // Will be set by relationship
            'user_id' => User::factory(),
            'issued_at' => $issuedAt,
            'valid_until' => $validUntil,
            'status' => $this->faker()->randomElement(QuoteStatus::cases()),
            'notes' => $this->faker()->optional()->sentence(),
            'terms' => $this->faker()->optional()->paragraph(),
            'subtotal' => 0, // Will be calculated
            'discount_total' => 0,
            'vat_total' => 0,
            'total' => 0,
            'sent_at' => null,
            'accepted_at' => null,
        ];
    }
}
