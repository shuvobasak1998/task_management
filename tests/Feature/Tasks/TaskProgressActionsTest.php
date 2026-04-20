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

        $response = $this->actingAs($user)->from('/tasks')->patch("/tasks/{$task->id}/progress", [
            'delta' => 25,
        ]);

        $response->assertRedirect('/tasks');
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

        $response = $this->actingAs($user)->from('/tasks')->patch("/tasks/{$task->id}/progress", [
            'target_progress' => 100,
        ]);

        $response->assertRedirect('/tasks');
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

        $response = $this->actingAs($user)->from('/tasks')->patch("/tasks/{$task->id}/progress", [
            'target_progress' => 90,
        ]);

        $response->assertRedirect('/tasks');
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'progress_percent' => 90,
            'status' => TaskStatus::InProgress->value,
        ]);
    }

    public function test_create_task_from_dashboard_returns_to_dashboard(): void
    {
        $creator = User::factory()->create();

        $response = $this->actingAs($creator)->from('/dashboard')->post('/tasks', [
            'title' => 'Dashboard-created task',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('tasks', [
            'title' => 'Dashboard-created task',
            'created_by' => $creator->id,
        ]);
    }

    public function test_create_task_from_workspace_returns_to_workspace(): void
    {
        $creator = User::factory()->create();

        $response = $this->actingAs($creator)->from('/tasks')->post('/tasks', [
            'title' => 'Workspace-created task',
        ]);

        $response->assertRedirect('/tasks');
        $this->assertDatabaseHas('tasks', [
            'title' => 'Workspace-created task',
            'created_by' => $creator->id,
        ]);
    }

    public function test_delete_task_returns_to_previous_workspace_page(): void
    {
        $creator = User::factory()->create();
        $task = Task::factory()->create([
            'created_by' => $creator->id,
            'assigned_to' => null,
        ]);

        $response = $this->actingAs($creator)->from('/tasks')->delete("/tasks/{$task->id}");

        $response->assertRedirect('/tasks');
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
