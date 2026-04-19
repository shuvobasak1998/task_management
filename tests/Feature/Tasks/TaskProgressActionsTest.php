<?php

namespace Tests\Feature\Tasks;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskProgressActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_increment_task_progress(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'created_by' => $user->id,
            'assigned_to' => null,
            'progress_percent' => 20,
            'status' => TaskStatus::Pending,
        ]);

        $response = $this->actingAs($user)->patch("/tasks/{$task->id}/progress", [
            'delta' => 25,
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'progress_percent' => 45,
            'status' => TaskStatus::InProgress->value,
        ]);
    }

    public function test_setting_progress_to_one_hundred_completes_the_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'created_by' => $user->id,
            'progress_percent' => 80,
            'status' => TaskStatus::InProgress,
        ]);

        $response = $this->actingAs($user)->patch("/tasks/{$task->id}/progress", [
            'target_progress' => 100,
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'progress_percent' => 100,
            'status' => TaskStatus::Completed->value,
        ]);
    }

    public function test_reopen_action_moves_completed_task_back_to_in_progress(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->completed()->create([
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->patch("/tasks/{$task->id}/progress", [
            'target_progress' => 90,
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'progress_percent' => 90,
            'status' => TaskStatus::InProgress->value,
        ]);
    }
}
