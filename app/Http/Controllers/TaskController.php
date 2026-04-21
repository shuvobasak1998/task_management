<?php

namespace App\Http\Controllers;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\User;
use App\Services\Tasks\TaskPageDataBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TaskController extends Controller
{
    public function dashboard(Request $request, TaskPageDataBuilder $taskPageDataBuilder): View
    {
        return view('dashboard', $this->pageData($request, $taskPageDataBuilder));
    }

    public function index(Request $request, TaskPageDataBuilder $taskPageDataBuilder): View
    {
        return view('tasks.index', $this->pageData($request, $taskPageDataBuilder));
    }

    /**
     * @return array<string, mixed>
     */
    protected function pageData(Request $request, TaskPageDataBuilder $taskPageDataBuilder): array
    {
        /** @var User $user */
        $user = $request->user();

        return $taskPageDataBuilder->buildForUser(
            $user,
            $taskPageDataBuilder->filtersFromRequest($request),
        );
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $dueAt = isset($validated['due_at']) ? Carbon::parse($validated['due_at']) : null;
        $startedAt = isset($validated['started_at']) ? Carbon::parse($validated['started_at']) : now();

        Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? TaskStatus::Pending,
            'progress_percent' => $validated['progress_percent'] ?? 0,
            'priority' => $validated['priority'] ?? TaskPriority::Medium,
            'estimated_minutes' => $this->derivedEstimatedMinutes($startedAt, $dueAt),
            'started_at' => $startedAt,
            'due_at' => $dueAt,
            'created_by' => $request->user()->id,
            'assigned_to' => $validated['assigned_to'] ?? null,
        ]);

        return $this->redirectToPreviousPage($request)
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
        $validated = $request->validated();
        $dueAt = isset($validated['due_at']) ? Carbon::parse($validated['due_at']) : null;
        $startedAt = isset($validated['started_at'])
            ? Carbon::parse($validated['started_at'])
            : ($task->started_at ?? $task->created_at ?? now());

        $task->update([
            ...$validated,
            'estimated_minutes' => $this->derivedEstimatedMinutes($task->created_at ?? $startedAt, $dueAt),
            'started_at' => $startedAt,
            'due_at' => $dueAt,
        ]);

        return redirect()->route('tasks.index')
            ->with('status', 'Task updated successfully.');
    }

    public function progress(Request $request, Task $task): RedirectResponse|JsonResponse
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

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Task progress updated successfully.',
                'task' => [
                    'id' => $task->id,
                    'progress_percent' => $task->progress_percent,
                    'status' => $task->status->value,
                    'status_label' => str($task->status->value)->replace('_', ' ')->title()->toString(),
                ],
            ]);
        }

        return $this->redirectToPreviousPage($request)
            ->with('status', 'Task progress updated successfully.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $this->authorize('delete', $task);

        $task->delete();

        return $this->redirectToPreviousPage(request())
            ->with('status', 'Task deleted successfully.');
    }

    protected function redirectToPreviousPage(Request $request, string $fallbackRoute = 'tasks.index'): RedirectResponse
    {
        $referer = $request->headers->get('referer');

        return redirect()->to($referer ?: route($fallbackRoute));
    }

    protected function derivedEstimatedMinutes(?Carbon $from, ?Carbon $dueAt): int
    {
        if (! $from instanceof Carbon || ! $dueAt instanceof Carbon || $dueAt->lte($from)) {
            return 0;
        }

        return (int) ceil($from->diffInSeconds($dueAt) / 60);
    }
}
