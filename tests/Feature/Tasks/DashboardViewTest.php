<?php

namespace Tests\Feature\Tasks;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_dashboard_analytics(): void
    {
        $user = User::factory()->create();
        $assignee = User::factory()->create();

        Task::factory()->create([
            'title' => 'Shared backlog grooming',
            'created_by' => $user->id,
            'assigned_to' => null,
            'status' => TaskStatus::InProgress,
        ]);

        Task::factory()->create([
            'title' => 'Assigned design QA',
            'created_by' => $assignee->id,
            'assigned_to' => $user->id,
            'status' => TaskStatus::Pending,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk()
            ->assertSee('Analytical clarity for the whole workspace.')
            ->assertSee('Assigned to me')
            ->assertSee('Created by me')
            ->assertSee('Monthly completed ratio')
            ->assertSee('Status distribution')
            ->assertSee('Overdue tasks')
            ->assertSee('Due soon')
            ->assertSee($user->name)
            ->assertSee($user->email);
    }

    public function test_dashboard_surfaces_overdue_and_due_soon_summaries(): void
    {
        $user = User::factory()->create();

        Task::factory()->create([
            'title' => 'Overdue migration',
            'created_by' => $user->id,
            'assigned_to' => null,
            'due_at' => now()->subHour(),
            'status' => TaskStatus::InProgress,
        ]);

        Task::factory()->create([
            'title' => 'Due soon review',
            'created_by' => $user->id,
            'assigned_to' => null,
            'due_at' => now()->addHours(6),
            'status' => TaskStatus::Pending,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk()
            ->assertSee('Overdue migration')
            ->assertSee('Due soon review');
    }

    public function test_dashboard_renders_create_task_entrypoint(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk()
            ->assertSee('Create a task from the dashboard')
            ->assertSee('Total tasks')
            ->assertSee('Assigned to me')
            ->assertSee('Created by me')
            ->assertSee('Due soon');
    }
}
