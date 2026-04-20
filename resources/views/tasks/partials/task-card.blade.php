@php($isOverdue = $task->isOverdue())

<article class="task-card-shell rounded-3xl border {{ $isOverdue ? 'border-rose-200 bg-rose-50/60' : 'border-stone-900/10 bg-white' }} p-5 shadow-sm">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div class="space-y-4">
            <div class="flex flex-wrap items-center gap-2">
                <x-tasks.status-badge :status="$task->status" />
                <x-tasks.priority-badge :priority="$task->priority" />
            </div>

            <div>
                <h3 class="text-xl font-semibold text-stone-950">{{ $task->title }}</h3>
                @if ($task->description)
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-stone-600">{{ $task->description }}</p>
                @endif
            </div>

            <div class="flex flex-wrap gap-5 text-sm text-stone-500">
                <p>Created by <span class="font-medium text-stone-700">{{ $task->creator->name }}</span></p>
                <p>Assigned to <span class="font-medium text-stone-700">{{ $task->assignee?->name ?? 'Unassigned' }}</span></p>
                <p>Created <span class="font-medium text-stone-700">{{ $task->created_at?->format('M d, Y H:i') ?? 'Unknown' }}</span></p>
                @if ($task->due_at)
                    <p>Due <span class="font-medium text-stone-700">{{ $task->due_at->format('M d, Y H:i') }}</span></p>
                @endif
            </div>

            <div class="grid gap-4 lg:grid-cols-[1fr_auto] lg:items-end">
                <x-tasks.progress-bar :progress="$task->progress_percent" />
                <x-tasks.timer-badge :task="$task" />
            </div>

            @if ($isOverdue)
                <div class="rounded-2xl border border-rose-200 bg-rose-100 px-4 py-3 text-sm text-rose-900">
                    This task is overdue. The due time has passed before the task was completed.
                </div>
            @endif
        </div>

        <x-tasks.action-group :task="$task" />
    </div>
</article>
