<?php

namespace App\Http\Controllers;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\User;
use App\Services\Tasks\TaskPageDataBuilder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
        $task->update($request->validated());

        return redirect()->route('tasks.index')
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
}
