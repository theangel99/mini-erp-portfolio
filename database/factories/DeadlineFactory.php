<?php

namespace Database\Factories;

use App\Enums\DeadlinePriority;
use App\Models\Deadline;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Deadline>
 */
class DeadlineFactory extends Factory
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
        return [
            'user_id' => User::factory(),
            'title' => $this->faker()->words(3, true),
            'description' => $this->faker()->optional()->sentence(),
            'due_at' => now()->addDays($this->faker()->numberBetween(1, 30)),
            'completed_at' => null,
            'priority' => $this->faker()->randomElement(DeadlinePriority::cases()),
            'project_id' => null,
            'milestone_id' => null,
        ];
    }

    public function dueToday(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_at' => now()->setTime($this->faker()->numberBetween(8, 18), $this->faker()->numberBetween(0, 59)),
        ]);
    }

    public function dueTomorrow(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_at' => now()->addDay()->setTime($this->faker()->numberBetween(8, 18), $this->faker()->numberBetween(0, 59)),
        ]);
    }

    public function dueThisWeek(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_at' => now()->addDays($this->faker()->numberBetween(2, 7))->setTime($this->faker()->numberBetween(8, 18), $this->faker()->numberBetween(0, 59)),
        ]);
    }

    public function dueNextMonth(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_at' => now()->addDays($this->faker()->numberBetween(15, 60))->setTime($this->faker()->numberBetween(8, 18), $this->faker()->numberBetween(0, 59)),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_at' => now()->subDays($this->faker()->numberBetween(1, 10))->setTime($this->faker()->numberBetween(8, 18), $this->faker()->numberBetween(0, 59)),
        ]);
    }

    public function completed(?int $daysLate = null): static
    {
        return $this->state(function (array $attributes) use ($daysLate) {
            $dueAt = $attributes['due_at'] ?? now();
            $completedAt = $daysLate !== null
                ? $dueAt->copy()->addDays($daysLate)
                : $dueAt->copy()->subDays($this->faker()->numberBetween(0, 2));

            return [
                'completed_at' => $completedAt,
            ];
        });
    }

    public function high(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => DeadlinePriority::High,
        ]);
    }
}
