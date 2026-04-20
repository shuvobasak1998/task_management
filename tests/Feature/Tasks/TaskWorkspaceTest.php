<?php

namespace Tests\Feature\Tasks;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_workspace_tasks_and_filters(): void
    {
        $user = User::factory()->create([
            'name' => 'Shuvo Basak',
            'email' => 'shuvo@gmail.com',
        ]);
        $teammate = User::factory()->create();

        Task::factory()->create([
            'title' => 'My planning task',
            'created_by' => $user->id,
            'assigned_to' => $teammate->id,
        ]);

        Task::factory()->create([
            'title' => 'Shared backlog grooming',
            'created_by' => $teammate->id,
            'assigned_to' => null,
        ]);

        $response = $this->actingAs($user)->get('/tasks');

        $response->assertOk()
            ->assertSee('Workspace for active delivery.')
            ->assertSee('My planning task')
            ->assertSee('Shared backlog grooming')
            ->assertSee('Search and filter the workspace')
            ->assertSee('Create a task without leaving the workspace')
            ->assertSee('Shuvo Basak')
            ->assertSee('shuvo@gmail.com');
    }

    public function test_workspace_surfaces_overdue_and_completed_timer_states(): void
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

        $response = $this->actingAs($user)->get('/tasks');

        $response->assertOk()
            ->assertSee('Expired')
            ->assertSee('Timer stopped');
    }

    public function test_workspace_filters_tasks_by_search_and_priority(): void
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

        $response = $this->actingAs($user)->get('/tasks?search=Launch&priority=high');

        $response->assertOk()
            ->assertSee('Launch marketing page')
            ->assertDontSee('Update onboarding docs');
    }

    public function test_workspace_can_filter_overdue_tasks_only(): void
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

        $response = $this->actingAs($user)->get('/tasks?overdue=1');

        $response->assertOk()
            ->assertSee('Expired support handoff')
            ->assertDontSee('Healthy design review');
    }

    public function test_workspace_empty_state_is_shown_when_filters_exclude_all_results(): void
    {
        $user = User::factory()->create();

        Task::factory()->create([
            'title' => 'Only visible task',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get('/tasks?search=NoMatchValue');

        $response->assertOk()
            ->assertSee('No tasks match these filters.')
            ->assertDontSee('Only visible task');
    }

    public function test_workspace_hides_mutation_actions_for_unauthorized_users(): void
    {
        $creator = User::factory()->create();
        $viewer = User::factory()->create();

        Task::factory()->create([
            'title' => 'Protected task',
            'created_by' => $creator->id,
            'assigned_to' => null,
            'status' => TaskStatus::Pending,
        ]);

        $response = $this->actingAs($viewer)->get('/tasks');

        $response->assertOk()
            ->assertSee('Protected task')
            ->assertDontSee('>Complete<', false)
            ->assertDontSee('>Delete<', false)
            ->assertDontSee('>Edit<', false);
    }
}
