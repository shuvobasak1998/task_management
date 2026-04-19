@php
    $isOverdue = $task->isOverdue();
    $remainingSeconds = $task->remainingSeconds();
    $timerLabel = $task->status === \App\Enums\TaskStatus::Completed
        ? 'Stopped'
        : ($isOverdue || $remainingSeconds === 0 ? 'Expired' : 'Live');

    $statusClasses = [
        'pending' => 'bg-stone-100 text-stone-700',
        'in_progress' => 'bg-amber-100 text-amber-900',
        'completed' => 'bg-emerald-100 text-emerald-900',
    ];

    $priorityClasses = [
        'low' => 'bg-sky-100 text-sky-900',
        'medium' => 'bg-orange-100 text-orange-900',
        'high' => 'bg-rose-100 text-rose-900',
    ];

    $formatCountdown = static function (int $seconds): string {
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $remaining = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $remaining);
    };
@endphp

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
        <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] {{ $statusClasses[$task->status->value] }}">
            {{ str($task->status->value)->replace('_', ' ') }}
        </span>
    </td>
    <td class="px-4 py-4 align-top">
        <div class="min-w-[140px]">
            <div class="flex items-center justify-between text-xs font-semibold uppercase tracking-[0.18em] text-stone-400">
                <span>Progress</span>
                <span class="text-white">{{ $task->progress_percent }}%</span>
            </div>
            <div class="mt-3 h-2.5 overflow-hidden rounded-full bg-white/10">
                <div class="h-full rounded-full bg-gradient-to-r from-amber-400 via-orange-500 to-emerald-500" style="width: {{ $task->progress_percent }}%"></div>
            </div>
        </div>
    </td>
    <td class="px-4 py-4 align-top">
        <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] {{ $priorityClasses[$task->priority->value] }}">
            {{ $task->priority->value }}
        </span>
    </td>
    <td class="px-4 py-4 align-top">
        <div class="min-w-[160px] text-sm text-stone-300">
            <p class="font-medium text-white">{{ $task->assignee?->name ?? 'Unassigned' }}</p>
            <p class="mt-1 text-xs uppercase tracking-[0.18em] text-stone-400">by {{ $task->creator->name }}</p>
        </div>
    </td>
    <td class="px-4 py-4 align-top">
        <div class="min-w-[120px] text-sm text-stone-300">
            <p class="font-medium text-white">{{ $task->due_at?->format('M d, Y') ?? 'No deadline' }}</p>
            <p class="mt-1 text-xs uppercase tracking-[0.18em] text-stone-400">{{ $task->estimated_minutes }} min</p>
        </div>
    </td>
    <td class="px-4 py-4 align-top">
        <div class="min-w-[115px] rounded-2xl border {{ $isOverdue ? 'border-rose-400/25 bg-rose-400/10' : 'border-white/10 bg-white/5' }} px-3 py-2">
            <p class="text-[10px] font-semibold uppercase tracking-[0.22em] {{ $isOverdue ? 'text-rose-200' : 'text-stone-400' }}">{{ $timerLabel }}</p>
            @if ($task->status === \App\Enums\TaskStatus::Completed)
                <p class="mt-1 text-sm font-semibold text-emerald-300">Timer stopped</p>
            @elseif ($isOverdue || $remainingSeconds === 0)
                <p class="mt-1 text-sm font-semibold text-rose-200">00:00:00</p>
            @else
                <p class="mt-1 text-sm font-semibold text-white" data-countdown-seconds="{{ $remainingSeconds }}">{{ $formatCountdown($remainingSeconds) }}</p>
            @endif
        </div>
    </td>
    <td class="px-4 py-4 align-top">
        <div class="flex min-w-[190px] flex-wrap gap-2">
            @can('update', $task)
                @if ($task->status === \App\Enums\TaskStatus::Completed)
                    <form method="POST" action="{{ route('tasks.progress', $task) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="target_progress" value="90">
                        <button type="submit" class="table-action">
                            Reopen
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('tasks.progress', $task) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="delta" value="10">
                        <button type="submit" class="table-action">
                            +10%
                        </button>
                    </form>

                    <form method="POST" action="{{ route('tasks.progress', $task) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="target_progress" value="100">
                        <button type="submit" class="table-action table-action-primary">
                            Complete
                        </button>
                    </form>
                @endif

                <a href="{{ route('tasks.edit', $task) }}" class="table-action">
                    Edit
                </a>
            @endcan

            @can('delete', $task)
                <form method="POST" action="{{ route('tasks.destroy', $task) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="table-action table-action-danger">
                        Delete
                    </button>
                </form>
            @endcan
        </div>
    </td>
</tr>
