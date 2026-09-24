<?php

namespace Database\Seeders;

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
        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@veberdigital.com',
        ]);

        // Create company settings
        \App\Models\CompanySetting::factory()->create();

        // Create customers with contacts
        \App\Models\Customer::factory(20)
            ->has(\App\Models\Contact::factory()->primary())
            ->has(\App\Models\Contact::factory()->count(rand(0, 2)))
            ->create();

        // Create products
        \App\Models\Product::factory(30)->create();
    }
}
