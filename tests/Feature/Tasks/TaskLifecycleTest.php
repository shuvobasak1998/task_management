<?php

namespace Tests\Feature\Tasks;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TaskLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_progress_reaching_one_hundred_marks_it_completed(): void
    {
        $task = Task::factory()->create([
            'progress_percent' => 100,
            'status' => TaskStatus::InProgress,
        ]);

        $this->assertSame(TaskStatus::Completed, $task->fresh()->status);
        $this->assertSame(100, $task->fresh()->progress_percent);
        $this->assertNotNull($task->fresh()->completed_at);
    }

    public function test_setting_status_to_completed_forces_progress_to_one_hundred(): void
    {
        $task = Task::factory()->create([
            'progress_percent' => 40,
            'status' => TaskStatus::Completed,
        ]);

        $this->assertSame(100, $task->fresh()->progress_percent);
        $this->assertSame(TaskStatus::Completed, $task->fresh()->status);
    }

    public function test_reopening_a_task_clears_completion_timestamp_when_progress_is_below_one_hundred(): void
    {
        $task = Task::factory()->completed()->create();

        $task->update([
            'status' => TaskStatus::InProgress,
            'progress_percent' => 70,
        ]);

        $this->assertSame(TaskStatus::InProgress, $task->fresh()->status);
        $this->assertNull($task->fresh()->completed_at);
    }

    public function test_task_reports_when_it_is_overdue(): void
    {
        Carbon::setTestNow(now());

        $task = Task::factory()->create([
            'due_at' => now()->subHour(),
            'status' => TaskStatus::InProgress,
        ]);

        $this->assertTrue($task->fresh()->isOverdue());
        $this->assertCount(1, Task::query()->overdue()->get());

        Carbon::setTestNow();
    }

    public function test_task_remaining_seconds_uses_completion_time_when_present(): void
    {
        Carbon::setTestNow('2026-04-20 08:00:00');

        $task = Task::factory()->for(User::factory(), 'creator')->create([
            'estimated_minutes' => 120,
            'started_at' => now()->subMinutes(100),
            'completed_at' => now()->subMinutes(30),
            'status' => TaskStatus::Completed,
            'progress_percent' => 100,
        ]);

        $this->assertSame(3000, $task->remainingSeconds());

        Carbon::setTestNow();
    }
}
