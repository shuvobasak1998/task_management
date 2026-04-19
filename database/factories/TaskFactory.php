<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'status' => TaskStatus::Pending,
            'progress_percent' => 0,
            'priority' => fake()->randomElement(TaskPriority::cases()),
            'estimated_minutes' => fake()->numberBetween(30, 240),
            'started_at' => now(),
            'due_at' => fake()->optional()->dateTimeBetween('now', '+7 days'),
            'completed_at' => null,
            'created_by' => User::factory(),
            'assigned_to' => User::factory(),
        ];
    }

    public function completed(): self
    {
        return $this->state([
            'status' => TaskStatus::Completed,
            'progress_percent' => 100,
            'completed_at' => now(),
        ]);
    }
}
