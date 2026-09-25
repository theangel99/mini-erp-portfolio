<?php

namespace Database\Factories;

use App\Enums\MilestoneStatus;
use App\Enums\WaitingOn;
use App\Models\Milestone;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Milestone>
 */
class MilestoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'sort' => 0,
            'title' => fake()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'planned_at' => now()->addDays(fake()->numberBetween(1, 30)),
            'completed_at' => null,
            'status' => MilestoneStatus::Upcoming,
            'waiting_on' => null,
            'waiting_note' => null,
            'waiting_since' => null,
        ];
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MilestoneStatus::InProgress,
            'planned_at' => now()->subDays(fake()->numberBetween(1, 5)),
        ]);
    }

    public function waiting(WaitingOn $waitingOn = WaitingOn::Customer, ?int $daysSince = null): static
    {
        $waitingSince = $daysSince !== null
            ? now()->subDays($daysSince)
            : now()->subDays(fake()->numberBetween(1, 10));

        return $this->state(fn (array $attributes) => [
            'status' => MilestoneStatus::Waiting,
            'waiting_on' => $waitingOn,
            'waiting_note' => fake()->sentence(),
            'waiting_since' => $waitingSince,
        ]);
    }

    public function done(?int $daysAgo = null): static
    {
        $completedAt = $daysAgo !== null
            ? now()->subDays($daysAgo)
            : now()->subDays(fake()->numberBetween(1, 30));

        return $this->state(fn (array $attributes) => [
            'status' => MilestoneStatus::Done,
            'completed_at' => $completedAt,
            'planned_at' => $completedAt->copy()->subDays(fake()->numberBetween(1, 10)),
        ]);
    }

    public function skipped(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MilestoneStatus::Skipped,
        ]);
    }
}
