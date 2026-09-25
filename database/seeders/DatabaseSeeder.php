<?php

namespace Database\Seeders;

use App\Models\CompanySetting;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user directly (without factory to avoid Faker issues)
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@veberdigital.com',
            'email_verified_at' => now(),
            'password' => bcrypt('geslo123'),
        ]);

        // Create company settings
        CompanySetting::factory()->create();

        // Create customers with contacts
        Customer::factory(20)
            ->has(Contact::factory()->primary())
            ->has(Contact::factory()->count(rand(0, 2)))
            ->create();

        // Create products
        Product::factory(30)->create();

        // Seed timeline demo data
        $this->call(TimelineSeeder::class);
    }
}
