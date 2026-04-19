@php
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
@endphp

<article class="rounded-3xl border border-stone-900/10 bg-white p-5 shadow-sm">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div class="space-y-4">
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] {{ $statusClasses[$task->status->value] }}">
                    {{ str($task->status->value)->replace('_', ' ') }}
                </span>
                <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] {{ $priorityClasses[$task->priority->value] }}">
                    {{ $task->priority->value }}
                </span>
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
                <p>Estimate <span class="font-medium text-stone-700">{{ $task->estimated_minutes }} min</span></p>
                @if ($task->due_at)
                    <p>Due <span class="font-medium text-stone-700">{{ $task->due_at->format('M d, Y H:i') }}</span></p>
                @endif
            </div>

            <div class="space-y-3">
                <div class="flex items-center justify-between gap-4 text-sm">
                    <span class="font-medium text-stone-700">Progress</span>
                    <span class="font-semibold text-stone-950">{{ $task->progress_percent }}%</span>
                </div>

                <div class="h-3 overflow-hidden rounded-full bg-stone-200">
                    <div class="h-full rounded-full bg-gradient-to-r from-amber-400 via-orange-500 to-emerald-500 transition-all" style="width: {{ $task->progress_percent }}%"></div>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-3 lg:max-w-xs lg:justify-end">
            @can('update', $task)
                @if ($task->status === \App\Enums\TaskStatus::Completed)
                    <form method="POST" action="{{ route('tasks.progress', $task) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="target_progress" value="90">
                        <button type="submit" class="inline-flex items-center rounded-full border border-amber-200 bg-amber-50 px-4 py-2 text-sm font-medium text-amber-900 transition hover:bg-amber-100">
                            Reopen at 90%
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('tasks.progress', $task) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="delta" value="10">
                        <button type="submit" class="inline-flex items-center rounded-full border border-stone-900/10 px-4 py-2 text-sm font-medium text-stone-700 transition hover:border-stone-900/30 hover:text-stone-950">
                            +10%
                        </button>
                    </form>

                    <form method="POST" action="{{ route('tasks.progress', $task) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="delta" value="25">
                        <button type="submit" class="inline-flex items-center rounded-full border border-stone-900/10 px-4 py-2 text-sm font-medium text-stone-700 transition hover:border-stone-900/30 hover:text-stone-950">
                            +25%
                        </button>
                    </form>

                    <form method="POST" action="{{ route('tasks.progress', $task) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="target_progress" value="100">
                        <button type="submit" class="inline-flex items-center rounded-full bg-stone-950 px-4 py-2 text-sm font-medium text-stone-50 transition hover:bg-stone-800">
                            Complete
                        </button>
                    </form>
                @endif
            @endcan

            @can('update', $task)
                <a href="{{ route('tasks.edit', $task) }}" class="inline-flex items-center rounded-full border border-stone-900/10 px-4 py-2 text-sm font-medium text-stone-700 transition hover:border-stone-900/30 hover:text-stone-950">
                    Edit
                </a>
            @endcan

            @can('delete', $task)
                <form method="POST" action="{{ route('tasks.destroy', $task) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center rounded-full border border-rose-200 px-4 py-2 text-sm font-medium text-rose-700 transition hover:bg-rose-50">
                        Delete
                    </button>
                </form>
            @endcan
        </div>
    </div>
</article>
