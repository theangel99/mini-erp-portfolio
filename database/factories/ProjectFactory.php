<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Models\Customer;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = now()->subDays($this->faker->numberBetween(10, 60));
        $endsAt = $startsAt->copy()->addDays($this->faker->numberBetween(30, 90));

        return [
            'name' => $this->faker->words(3, true),
            'customer_id' => Customer::factory(),
            'quote_id' => null,
            'description' => $this->faker->optional()->paragraph(),
            'status' => $this->faker->randomElement(ProjectStatus::cases()),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'user_id' => User::factory(),
        ];
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProjectStatus::InProgress,
            'starts_at' => now()->subDays($this->faker->numberBetween(5, 30)),
            'ends_at' => now()->addDays($this->faker->numberBetween(30, 60)),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProjectStatus::InProgress,
            'starts_at' => now()->subDays($this->faker->numberBetween(30, 60)),
            'ends_at' => now()->subDays($this->faker->numberBetween(1, 10)),
        ]);
    }
}
