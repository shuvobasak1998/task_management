<?php

namespace Tests\Feature\Tasks;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_dashboard_with_my_tasks_and_team_tasks(): void
    {
        $user = User::factory()->create();
        $teammate = User::factory()->create();

        $myTask = Task::factory()->create([
            'title' => 'My planning task',
            'created_by' => $user->id,
            'assigned_to' => $teammate->id,
        ]);

        $teamTask = Task::factory()->create([
            'title' => 'Shared backlog grooming',
            'created_by' => $teammate->id,
            'assigned_to' => null,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk()
            ->assertSee('My planning task')
            ->assertSee('Shared backlog grooming')
            ->assertSee('Tasks connected to you')
            ->assertSee('Shared workspace activity');
    }

    public function test_authenticated_user_can_create_task(): void
    {
        $creator = User::factory()->create();
        $assignee = User::factory()->create();

        $response = $this->actingAs($creator)->post('/tasks', [
            'title' => 'Prepare release checklist',
            'description' => 'Document all launch tasks.',
            'status' => TaskStatus::Pending->value,
            'priority' => TaskPriority::High->value,
            'estimated_minutes' => 90,
            'assigned_to' => $assignee->id,
            'due_at' => now()->addDay()->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Prepare release checklist',
            'created_by' => $creator->id,
            'assigned_to' => $assignee->id,
            'priority' => TaskPriority::High->value,
        ]);
    }

    public function test_creator_can_update_task_details(): void
    {
        $creator = User::factory()->create();
        $assignee = User::factory()->create();
        $task = Task::factory()->create([
            'created_by' => $creator->id,
            'assigned_to' => $assignee->id,
        ]);

        $response = $this->actingAs($creator)->patch("/tasks/{$task->id}", [
            'title' => 'Updated task title',
            'description' => 'Updated notes',
            'status' => TaskStatus::InProgress->value,
            'progress_percent' => 30,
            'priority' => TaskPriority::Medium->value,
            'estimated_minutes' => 75,
            'due_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'assigned_to' => $assignee->id,
        ]);

        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated task title',
            'status' => TaskStatus::InProgress->value,
            'progress_percent' => 30,
        ]);
    }

    public function test_creator_can_delete_task(): void
    {
        $creator = User::factory()->create();
        $task = Task::factory()->create([
            'created_by' => $creator->id,
            'assigned_to' => null,
        ]);

        $response = $this->actingAs($creator)->delete("/tasks/{$task->id}");

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
