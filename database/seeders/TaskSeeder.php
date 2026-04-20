<?php

namespace Database\Seeders;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TaskSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's tasks table with demo task states.
     */
    public function run(): void
    {
        $users = User::query()->take(10)->get();

        if ($users->isEmpty()) {
            $users = User::factory()->count(10)->create();
        }

        $now = Carbon::now();

        $tasks = [
            [
                'title' => 'Prepare sprint kickoff agenda',
                'description' => 'Collect updates and organize the kickoff meeting notes.',
                'status' => TaskStatus::Pending,
                'progress_percent' => 0,
                'priority' => TaskPriority::High,
                'created_at' => $now->copy()->subHours(8),
                'due_at' => $now->copy()->addHours(10),
            ],
            [
                'title' => 'Review onboarding checklist',
                'description' => 'Validate the latest onboarding flow for new team members.',
                'status' => TaskStatus::InProgress,
                'progress_percent' => 35,
                'priority' => TaskPriority::Medium,
                'created_at' => $now->copy()->subDay(),
                'due_at' => $now->copy()->addHours(6),
            ],
            [
                'title' => 'Close monthly reporting pack',
                'description' => 'Finalize report files and hand them off to finance.',
                'status' => TaskStatus::Completed,
                'progress_percent' => 100,
                'priority' => TaskPriority::High,
                'created_at' => $now->copy()->subDays(3),
                'due_at' => $now->copy()->subDay(),
                'completed_at' => $now->copy()->subDays(2)->addHours(4),
            ],
            [
                'title' => 'Fix production navbar spacing',
                'description' => 'Patch the layout issue reported by the client on smaller laptops.',
                'status' => TaskStatus::InProgress,
                'progress_percent' => 60,
                'priority' => TaskPriority::High,
                'created_at' => $now->copy()->subHours(5),
                'due_at' => $now->copy()->addHours(2),
            ],
            [
                'title' => 'Archive outdated design assets',
                'description' => 'Move retired assets to the archive and update references.',
                'status' => TaskStatus::Pending,
                'progress_percent' => 0,
                'priority' => TaskPriority::Low,
                'created_at' => $now->copy()->subDays(2),
                'due_at' => $now->copy()->subHours(3),
            ],
            [
                'title' => 'Publish release notes',
                'description' => 'Write and publish release highlights for the latest deployment.',
                'status' => TaskStatus::Completed,
                'progress_percent' => 100,
                'priority' => TaskPriority::Medium,
                'created_at' => $now->copy()->subDays(4),
                'due_at' => $now->copy()->subDays(2)->addHours(3),
                'completed_at' => $now->copy()->subDays(2),
            ],
            [
                'title' => 'QA check for task filters',
                'description' => 'Verify search and filter combinations on the workspace page.',
                'status' => TaskStatus::InProgress,
                'progress_percent' => 15,
                'priority' => TaskPriority::High,
                'created_at' => $now->copy()->subHours(2),
                'due_at' => $now->copy()->addDay()->setTime(12, 0),
            ],
            [
                'title' => 'Draft support reply templates',
                'description' => 'Create reusable responses for the most common support cases.',
                'status' => TaskStatus::Pending,
                'progress_percent' => 0,
                'priority' => TaskPriority::Medium,
                'created_at' => $now->copy()->subHours(10),
                'due_at' => $now->copy()->addDays(2),
            ],
            [
                'title' => 'Restore broken image uploads',
                'description' => 'Investigate and resolve the image upload issue for task attachments.',
                'status' => TaskStatus::InProgress,
                'progress_percent' => 75,
                'priority' => TaskPriority::High,
                'created_at' => $now->copy()->subDay()->subHours(6),
                'due_at' => $now->copy()->subMinutes(30),
            ],
            [
                'title' => 'Clean up stale notifications',
                'description' => 'Remove old notification records and confirm the dashboard count is correct.',
                'status' => TaskStatus::Completed,
                'progress_percent' => 100,
                'priority' => TaskPriority::Low,
                'created_at' => $now->copy()->subDays(5),
                'due_at' => $now->copy()->subDays(3),
                'completed_at' => $now->copy()->subDays(3)->subHours(2),
            ],
        ];

        foreach ($tasks as $index => $taskData) {
            $creator = $users[$index % $users->count()];
            $assignee = $users[($index + 1) % $users->count()];
            $createdAt = $taskData['created_at'];
            $dueAt = $taskData['due_at'];

            Task::query()->create([
                'title' => $taskData['title'],
                'description' => $taskData['description'],
                'status' => $taskData['status'],
                'progress_percent' => $taskData['progress_percent'],
                'priority' => $taskData['priority'],
                'estimated_minutes' => max(30, $createdAt->diffInMinutes($dueAt, false)),
                'started_at' => $createdAt,
                'due_at' => $dueAt,
                'completed_at' => $taskData['completed_at'] ?? null,
                'created_by' => $creator->id,
                'assigned_to' => $assignee->id,
                'created_at' => $createdAt,
                'updated_at' => $taskData['completed_at'] ?? $now,
            ]);
        }
    }
}
