<?php

namespace Tests\Feature\Tasks;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_creator_can_update_and_delete_a_task(): void
    {
        $creator = User::factory()->create();
        $task = Task::factory()->create([
            'created_by' => $creator->id,
            'assigned_to' => null,
        ]);

        $this->assertTrue($creator->can('update', $task));
        $this->assertTrue($creator->can('delete', $task));
    }

    public function test_assignee_can_update_but_cannot_delete_a_task(): void
    {
        $creator = User::factory()->create();
        $assignee = User::factory()->create();
        $task = Task::factory()->create([
            'created_by' => $creator->id,
            'assigned_to' => $assignee->id,
        ]);

        $this->assertTrue($assignee->can('update', $task));
        $this->assertFalse($assignee->can('delete', $task));
    }

    public function test_other_team_member_cannot_update_or_delete_a_task(): void
    {
        $task = Task::factory()->create();
        $otherUser = User::factory()->create();

        $this->assertFalse($otherUser->can('update', $task));
        $this->assertFalse($otherUser->can('delete', $task));
    }
}
