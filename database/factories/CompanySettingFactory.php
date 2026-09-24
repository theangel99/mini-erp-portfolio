<?php

namespace Database\Factories;

use App\Models\CompanySetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompanySetting>
 */
class CompanySettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Veber Digital d.o.o.',
            'address' => 'Slovenčeva ulica 12',
            'postal_code' => '1000',
            'city' => 'Ljubljana',
            'vat_id' => 'SI12345678',
            'registration_number' => '1234567000',
            'iban' => 'SI56 0110 0100 0123 456',
            'bic' => 'BSLJSI2X',
            'bank' => 'Banka Slovenije',
            'email' => 'info@veberdigital.com',
            'phone' => '+386 1 234 5678',
            'logo' => null,
            'quote_prefix' => 'P',
            'invoice_prefix' => '',
            'default_payment_days' => 30,
            'document_footer' => 'Hvala za poslovanje!',
        ];
    }
}
