@props([
    'progress',
    'task' => null,
    'compact' => false,
])

@php
    $isInteractive = $task && auth()->user()?->can('update', $task);
    $wrapperClasses = $compact ? 'min-w-[140px]' : 'space-y-3';
    $trackClasses = $compact
        ? 'mt-3 h-2.5 overflow-hidden rounded-full bg-white/10'
        : 'h-3 overflow-hidden rounded-full bg-stone-200';
    $fillClasses = $compact
        ? 'h-full rounded-full bg-gradient-to-r from-amber-400 via-orange-500 to-emerald-500 transition-all'
        : 'h-full rounded-full bg-gradient-to-r from-amber-400 via-orange-500 to-emerald-500 transition-all';
@endphp

@if ($compact)
    <div
        class="{{ $wrapperClasses }}"
        @if ($isInteractive)
            data-progress-control
            data-progress-url="{{ route('tasks.progress', $task) }}"
            data-progress-value="{{ $progress }}"
            data-progress-task-id="{{ $task->id }}"
            data-progress-csrf="{{ csrf_token() }}"
        @endif
    >
        <div class="flex items-center justify-between text-xs font-semibold uppercase tracking-[0.18em] text-stone-400">
            <span>Progress</span>
            <span class="text-white" data-progress-value-label>{{ $progress }}%</span>
        </div>
        <div
            class="{{ $trackClasses }} {{ $isInteractive ? 'cursor-ew-resize select-none touch-none' : '' }}"
            @if ($isInteractive)
                data-progress-track
                role="slider"
                tabindex="0"
                aria-label="Update task progress"
                aria-valuemin="0"
                aria-valuemax="100"
                aria-valuenow="{{ $progress }}"
            @endif
        >
            <div class="{{ $fillClasses }}" data-progress-fill style="width: {{ $progress }}%"></div>
        </div>
    </div>
@else
    <div
        class="{{ $wrapperClasses }}"
        @if ($isInteractive)
            data-progress-control
            data-progress-url="{{ route('tasks.progress', $task) }}"
            data-progress-value="{{ $progress }}"
            data-progress-task-id="{{ $task->id }}"
            data-progress-csrf="{{ csrf_token() }}"
        @endif
    >
        <div class="flex items-center justify-between gap-4 text-sm">
            <span class="font-medium text-stone-700">Progress</span>
            <span class="font-semibold text-stone-950" data-progress-value-label>{{ $progress }}%</span>
        </div>

        <div
            class="{{ $trackClasses }} {{ $isInteractive ? 'cursor-ew-resize select-none touch-none' : '' }}"
            @if ($isInteractive)
                data-progress-track
                role="slider"
                tabindex="0"
                aria-label="Update task progress"
                aria-valuemin="0"
                aria-valuemax="100"
                aria-valuenow="{{ $progress }}"
            @endif
        >
            <div class="{{ $fillClasses }}" data-progress-fill style="width: {{ $progress }}%"></div>
        </div>
    </div>
@endif
