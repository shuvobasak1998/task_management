<?php

namespace Tests\Feature\Tasks;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskAuthorizationAndValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_create_task(): void
    {
        $response = $this->post('/tasks', [
            'title' => 'Blocked guest task',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_invalid_task_creation_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from('/dashboard')->post('/tasks', [
            'title' => '',
            'priority' => 'urgent',
            'status' => 'done',
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHasErrors(['title', 'priority', 'status']);
    }

    public function test_non_owner_cannot_update_task_details(): void
    {
        $creator = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->create([
            'created_by' => $creator->id,
            'assigned_to' => null,
        ]);

        $response = $this->actingAs($otherUser)->patch("/tasks/{$task->id}", [
            'title' => 'Malicious edit',
            'description' => 'Should fail',
            'status' => 'pending',
            'progress_percent' => 10,
            'priority' => 'low',
            'assigned_to' => null,
        ]);

        $response->assertForbidden();
    }

    public function test_non_owner_cannot_delete_task(): void
    {
        $creator = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->create([
            'created_by' => $creator->id,
            'assigned_to' => null,
        ]);

        $response = $this->actingAs($otherUser)->delete("/tasks/{$task->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    public function test_non_owner_cannot_use_quick_progress_actions(): void
    {
        $creator = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->create([
            'created_by' => $creator->id,
            'assigned_to' => null,
            'progress_percent' => 20,
        ]);

        $response = $this->actingAs($otherUser)->patch("/tasks/{$task->id}/progress", [
            'delta' => 10,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'progress_percent' => 20,
        ]);
    }
}
