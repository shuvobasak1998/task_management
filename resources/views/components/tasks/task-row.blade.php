@props([
    'task',
])

@php($isOverdue = $task->isOverdue())

<tr class="border-t border-white/10 transition hover:bg-white/5">
    <td class="px-4 py-4 align-top">
        <div class="min-w-[220px]">
            <div class="flex items-center gap-2">
                <p class="font-semibold text-white">{{ $task->title }}</p>
                @if ($isOverdue)
                    <span class="rounded-full bg-rose-100 px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.2em] text-rose-700">Overdue</span>
                @endif
            </div>
            @if ($task->description)
                <p class="mt-1 max-w-md text-sm leading-6 text-stone-400">{{ \Illuminate\Support\Str::limit($task->description, 90) }}</p>
            @endif
        </div>
    </td>
    <td class="px-4 py-4 align-top">
        <x-tasks.status-badge :status="$task->status" />
    </td>
    <td class="px-4 py-4 align-top">
        <x-tasks.progress-bar :progress="$task->progress_percent" compact="true" />
    </td>
    <td class="px-4 py-4 align-top">
        <x-tasks.priority-badge :priority="$task->priority" />
    </td>
    <td class="px-4 py-4 align-top">
        <div class="min-w-[160px] text-sm text-stone-300">
            <p class="font-medium text-white">{{ $task->assignee?->name ?? 'Unassigned' }}</p>
            <p class="mt-1 text-xs uppercase tracking-[0.18em] text-stone-400">by {{ $task->creator->name }}</p>
        </div>
    </td>
    <td class="px-4 py-4 align-top">
        <div class="min-w-[180px] text-sm text-stone-300">
            <p class="font-medium text-white">{{ $task->due_at?->format('M d, Y h:i A') ?? 'No deadline' }}</p>
            <p class="mt-1 text-xs uppercase tracking-[0.18em] text-stone-400">
                Created {{ $task->created_at?->format('M d, Y h:i A') ?? 'unknown' }}
            </p>
        </div>
    </td>
    <td class="px-4 py-4 align-top">
        <x-tasks.timer-badge :task="$task" compact="true" />
    </td>
    <td class="px-4 py-4 align-top">
        <x-tasks.action-group :task="$task" compact="true" />
    </td>
</tr>
