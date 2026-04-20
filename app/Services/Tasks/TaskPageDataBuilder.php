<?php

namespace App\Services\Tasks;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TaskPageDataBuilder
{
    /**
     * @return array{search: ?string, status: ?string, priority: ?string, assigned_to: ?string, overdue: bool}
     */
    public function filtersFromRequest(Request $request): array
    {
        return [
            'search' => $request->string('search')->toString() ?: null,
            'status' => $request->string('status')->toString() ?: null,
            'priority' => $request->string('priority')->toString() ?: null,
            'assigned_to' => $request->string('assigned_to')->toString() ?: null,
            'overdue' => $request->boolean('overdue'),
        ];
    }

    /**
     * @param array{search: ?string, status: ?string, priority: ?string, assigned_to: ?string, overdue: bool} $filters
     * @return array<string, mixed>
     */
    public function buildForUser(User $user, array $filters): array
    {
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

        $overdueTasks = $allTasks->filter(fn (Task $task): bool => $task->isOverdue())->values();
        $dueSoonTasks = $allTasks->filter(fn (Task $task): bool => $task->isDueSoon())->values();
        $distributionChart = $this->distributionChart($allTasks);
        $monthlyCompletionChart = $this->monthlyCompletionChart($allTasks);
        $dashboardStats = [
            ['label' => 'Total tasks', 'value' => $allTasks->count(), 'variant' => 'default'],
            ['label' => 'My tasks', 'value' => $myTasksCount, 'variant' => 'default'],
            ['label' => 'Active now', 'value' => $allTasks->where('status', TaskStatus::InProgress)->count(), 'variant' => 'default'],
            ['label' => 'Completed', 'value' => $allTasks->where('status', TaskStatus::Completed)->count(), 'variant' => 'default'],
            ['label' => 'Overdue', 'value' => $overdueTasks->count(), 'variant' => 'alert'],
            ['label' => 'Due soon', 'value' => $dueSoonTasks->count(), 'variant' => 'accent'],
        ];

        return [
            'filteredTasks' => $filteredTasks,
            'allTasks' => $allTasks,
            'myTasksCount' => $myTasksCount,
            'dashboardStats' => $dashboardStats,
            'overdueTasks' => $overdueTasks,
            'dueSoonTasks' => $dueSoonTasks,
            'users' => User::query()->orderBy('name')->get(),
            'priorities' => TaskPriority::cases(),
            'statuses' => TaskStatus::cases(),
            'filters' => $filters,
            'filteredTaskCount' => $filteredTasks->count(),
            'monthlyCompletionChart' => $monthlyCompletionChart,
            'monthlyCompletionPeak' => max(1, $monthlyCompletionChart->max('ratio')),
            'distributionChart' => $distributionChart,
            'distributionChartMeta' => $this->distributionChartMeta($distributionChart),
        ];
    }

    /**
     * @param \Illuminate\Support\Collection<int, Task> $tasks
     * @return \Illuminate\Support\Collection<int, array{label: string, created: int, completed: int, ratio: int}>
     */
    protected function monthlyCompletionChart($tasks)
    {
        return collect(range(5, 0))->map(function (int $monthsAgo) use ($tasks): array {
            $month = Carbon::now()->subMonths($monthsAgo)->startOfMonth();
            $created = $tasks->filter(
                fn (Task $task): bool => $task->created_at instanceof Carbon && $task->created_at->isSameMonth($month),
            )->count();
            $completed = $tasks->filter(
                fn (Task $task): bool => $task->completed_at instanceof Carbon && $task->completed_at->isSameMonth($month),
            )->count();

            return [
                'label' => $month->format('M'),
                'created' => $created,
                'completed' => $completed,
                'ratio' => $created > 0 ? (int) round(($completed / $created) * 100) : 0,
            ];
        })->values();
    }

    /**
     * @param \Illuminate\Support\Collection<int, Task> $tasks
     * @return array<int, array{label: string, value: int, color: string}>
     */
    protected function distributionChart($tasks): array
    {
        return [
            ['label' => 'Completed', 'value' => $tasks->where('status', TaskStatus::Completed)->count(), 'color' => '#15803d'],
            ['label' => 'Overdue', 'value' => $tasks->filter(fn (Task $task): bool => $task->isOverdue())->count(), 'color' => '#be123c'],
            ['label' => 'Active now', 'value' => $tasks->where('status', TaskStatus::InProgress)->count(), 'color' => '#b45309'],
        ];
    }

    /**
     * @param array<int, array{label: string, value: int, color: string}> $distributionChart
     * @return array{circumference: float, slices: array<int, array{label: string, value: int, color: string, length: float|int, offset: float|int}>, total: int}
     */
    protected function distributionChartMeta(array $distributionChart): array
    {
        $total = max(1, collect($distributionChart)->sum('value'));
        $circumference = 282.74;

        $slices = collect($distributionChart)->values()->reduce(function ($carry, $item) use ($total, $circumference) {
            $length = ($item['value'] / $total) * $circumference;
            $carry[] = [
                ...$item,
                'length' => $length,
                'offset' => -collect($carry)->sum('length'),
            ];

            return $carry;
        }, []);

        return [
            'circumference' => $circumference,
            'slices' => $slices,
            'total' => collect($distributionChart)->sum('value'),
        ];
    }
}
