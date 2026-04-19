<?php

namespace App\Http\Controllers;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $filters = [
            'search' => request('search'),
            'status' => request('status'),
            'priority' => request('priority'),
            'assigned_to' => request('assigned_to'),
            'overdue' => request()->boolean('overdue'),
        ];

        $filteredTasks = Task::query()
            ->with(['creator', 'assignee'])
            ->search($filters['search'])
            ->forStatus($filters['status'])
            ->forPriority($filters['priority'])
            ->assignedTo($filters['assigned_to'] ? (int) $filters['assigned_to'] : null)
            ->when($filters['overdue'], fn ($query) => $query->overdue())
            ->latest()
            ->get();

        $allTasks = Task::query()
            ->with(['creator', 'assignee'])
            ->latest()
            ->get();

        $myTasksCount = $allTasks->filter(
            fn (Task $task): bool => $task->created_by === $user->id || $task->assigned_to === $user->id,
        )->count();

        $monthlyCompletionChart = collect(range(5, 0))->map(function (int $monthsAgo) use ($allTasks): array {
            $month = Carbon::now()->subMonths($monthsAgo)->startOfMonth();
            $created = $allTasks->filter(
                fn (Task $task): bool => $task->created_at instanceof Carbon && $task->created_at->isSameMonth($month),
            )->count();
            $completed = $allTasks->filter(
                fn (Task $task): bool => $task->completed_at instanceof Carbon && $task->completed_at->isSameMonth($month),
            )->count();

            return [
                'label' => $month->format('M'),
                'created' => $created,
                'completed' => $completed,
                'ratio' => $created > 0 ? (int) round(($completed / $created) * 100) : 0,
            ];
        })->values();

        $distributionChart = [
            ['label' => 'Completed', 'value' => $allTasks->where('status', TaskStatus::Completed)->count(), 'color' => '#15803d'],
            ['label' => 'Overdue', 'value' => $allTasks->filter(fn (Task $task): bool => $task->isOverdue())->count(), 'color' => '#be123c'],
            ['label' => 'Active now', 'value' => $allTasks->where('status', TaskStatus::InProgress)->count(), 'color' => '#b45309'],
        ];

        return view('tasks.index', [
            'filteredTasks' => $filteredTasks,
            'allTasks' => $allTasks,
            'myTasksCount' => $myTasksCount,
            'users' => User::query()->orderBy('name')->get(),
            'priorities' => TaskPriority::cases(),
            'statuses' => TaskStatus::cases(),
            'filters' => $filters,
            'monthlyCompletionChart' => $monthlyCompletionChart,
            'distributionChart' => $distributionChart,
        ]);
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? TaskStatus::Pending,
            'progress_percent' => $validated['progress_percent'] ?? 0,
            'priority' => $validated['priority'] ?? TaskPriority::Medium,
            'estimated_minutes' => $validated['estimated_minutes'],
            'started_at' => $validated['started_at'] ?? now(),
            'due_at' => $validated['due_at'] ?? null,
            'created_by' => $request->user()->id,
            'assigned_to' => $validated['assigned_to'] ?? null,
        ]);

        return redirect()->route('dashboard')
            ->with('status', 'Task created successfully.');
    }

    public function edit(Task $task): View
    {
        $this->authorize('update', $task);

        return view('tasks.edit', [
            'task' => $task->load(['creator', 'assignee']),
            'users' => User::query()->orderBy('name')->get(),
            'priorities' => TaskPriority::cases(),
            'statuses' => TaskStatus::cases(),
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $task->update($request->validated());

        return redirect()->route('dashboard')
            ->with('status', 'Task updated successfully.');
    }

    public function progress(Request $request, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'delta' => ['nullable', 'integer', 'min:1', 'max:100'],
            'target_progress' => ['nullable', 'integer', 'between:0,100'],
        ]);

        $targetProgress = $validated['target_progress']
            ?? min(100, $task->progress_percent + ($validated['delta'] ?? 0));

        $status = $targetProgress >= 100
            ? TaskStatus::Completed
            : ($targetProgress > 0 ? TaskStatus::InProgress : TaskStatus::Pending);

        $task->update([
            'progress_percent' => $targetProgress,
            'status' => $status,
        ]);

        return redirect()->route('dashboard')
            ->with('status', 'Task progress updated successfully.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $this->authorize('delete', $task);

        $task->delete();

        return redirect()->route('dashboard')
            ->with('status', 'Task deleted successfully.');
    }
}
