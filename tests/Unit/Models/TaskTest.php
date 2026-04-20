<?php

namespace Tests\Unit\Models;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_progress_is_capped_and_completion_state_is_synchronized(): void
    {
        $task = Task::factory()->create([
            'progress_percent' => 140,
            'status' => TaskStatus::InProgress,
        ]);

        $this->assertSame(100, $task->fresh()->progress_percent);
        $this->assertSame(TaskStatus::Completed, $task->fresh()->status);
        $this->assertNotNull($task->fresh()->completed_at);
    }

    public function test_due_soon_only_returns_future_non_completed_tasks_within_a_day(): void
    {
        Carbon::setTestNow('2026-04-21 10:00:00');

        $dueSoonTask = Task::factory()->create([
            'due_at' => now()->addHours(6),
            'status' => TaskStatus::InProgress,
        ]);

        Task::factory()->create([
            'due_at' => now()->addDays(2),
            'status' => TaskStatus::InProgress,
        ]);

        Task::factory()->completed()->create([
            'due_at' => now()->addHours(3),
        ]);

        $this->assertTrue($dueSoonTask->fresh()->isDueSoon());
        $this->assertSame([$dueSoonTask->id], Task::query()->dueSoon()->pluck('id')->all());

        Carbon::setTestNow();
    }

    public function test_remaining_seconds_uses_started_at_or_created_at_as_fallback(): void
    {
        Carbon::setTestNow('2026-04-21 10:00:00');

        $task = new Task([
            'title' => 'Fallback timing task',
            'status' => TaskStatus::Pending,
            'progress_percent' => 0,
            'estimated_minutes' => 120,
            'created_by' => User::factory()->create()->id,
        ]);
        $task->created_at = now()->subMinutes(30);

        $this->assertSame(5400, $task->remainingSeconds());

        Carbon::setTestNow();
    }
}
