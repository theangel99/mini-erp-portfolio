<?php

namespace Database\Factories;

use App\Models\QuoteItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuoteItem>
 */
class QuoteItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = \App\Models\Product::inRandomOrder()->first();
        $quantity = $this->faker->randomFloat(3, 1, 100);
        $unitPrice = $product ? (float) $product->price : $this->faker->randomFloat(2, 10, 1000);
        $discountPercent = $this->faker->randomElement([0, 0, 0, 5, 10, 15]);
        $vatRate = $product ? $product->vat_rate : $this->faker->randomElement(\App\Enums\VatRate::cases());

        // Calculate line totals
        $calculated = \App\Services\DocumentTotalsCalculator::calculateLineItem([
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount_percent' => $discountPercent,
            'vat_rate' => $vatRate,
        ]);

        return [
            'quote_id' => \App\Models\Quote::factory(),
            'product_id' => $product?->id,
            'sort' => 0,
            'description' => $product ? ($product->description ?? $product->name) : $this->faker->sentence(),
            'unit' => $product ? $product->unit : $this->faker->randomElement(\App\Enums\ProductUnit::cases()),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount_percent' => $discountPercent,
            'vat_rate' => $vatRate,
            'line_net' => $calculated['line_net'],
            'line_vat' => $calculated['line_vat'],
            'line_total' => $calculated['line_total'],
        ];
    }
}
