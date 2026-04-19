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
            ->assertSee('Search and filter the workspace')
            ->assertSee('Monthly completed ratio')
            ->assertSee('Status distribution');
    }

    public function test_dashboard_surfaces_overdue_and_completed_timer_states(): void
    {
        $user = User::factory()->create();

        Task::factory()->create([
            'title' => 'Overdue migration',
            'created_by' => $user->id,
            'assigned_to' => null,
            'due_at' => now()->subHour(),
            'status' => TaskStatus::InProgress,
        ]);

        Task::factory()->completed()->create([
            'title' => 'Completed launch review',
            'created_by' => $user->id,
            'assigned_to' => null,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk()
            ->assertSee('A smarter view of team execution.')
            ->assertSee('Expired')
            ->assertSee('Timer stopped');
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

    public function test_dashboard_filters_tasks_by_search_and_priority(): void
    {
        $user = User::factory()->create();

        Task::factory()->create([
            'title' => 'Launch marketing page',
            'description' => 'High priority landing page',
            'priority' => TaskPriority::High,
            'created_by' => $user->id,
            'assigned_to' => null,
        ]);

        Task::factory()->create([
            'title' => 'Update onboarding docs',
            'description' => 'Internal notes refresh',
            'priority' => TaskPriority::Low,
            'created_by' => $user->id,
            'assigned_to' => null,
        ]);

        $response = $this->actingAs($user)->get('/dashboard?search=Launch&priority=high');

        $response->assertOk()
            ->assertSee('Launch marketing page')
            ->assertDontSee('Update onboarding docs');
    }

    public function test_dashboard_can_filter_overdue_tasks_only(): void
    {
        $user = User::factory()->create();

        Task::factory()->create([
            'title' => 'Expired support handoff',
            'due_at' => now()->subHour(),
            'status' => TaskStatus::InProgress,
            'created_by' => $user->id,
            'assigned_to' => null,
        ]);

        Task::factory()->create([
            'title' => 'Healthy design review',
            'due_at' => now()->addHour(),
            'status' => TaskStatus::InProgress,
            'created_by' => $user->id,
            'assigned_to' => null,
        ]);

        $response = $this->actingAs($user)->get('/dashboard?overdue=1');

        $response->assertOk()
            ->assertSee('Expired support handoff')
            ->assertDontSee('Healthy design review');
    }

    public function test_dashboard_renders_create_task_modal_and_metrics(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk()
            ->assertSee('Create a task without leaving the workspace')
            ->assertSee('Total tasks')
            ->assertSee('Due soon');
    }
}
