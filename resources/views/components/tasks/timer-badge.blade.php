@props([
    'task',
    'compact' => false,
])

@php
    $isOverdue = $task->isOverdue();
    $remainingSeconds = $task->remainingSeconds();
    $timerLabel = $task->status === \App\Enums\TaskStatus::Completed
        ? 'Stopped'
        : ($isOverdue || $remainingSeconds === 0 ? 'Expired' : 'Live');
    $formatCountdown = static function (int $seconds): string {
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $remaining = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $remaining);
    };
@endphp

@if ($compact)
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
@else
    <div class="rounded-2xl border {{ $isOverdue ? 'border-rose-200 bg-rose-100 text-rose-900' : 'border-stone-900/10 bg-stone-50 text-stone-900' }} px-4 py-3">
        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] {{ $isOverdue ? 'text-rose-700' : 'text-stone-500' }}">Live clock</p>
        @if ($task->status === \App\Enums\TaskStatus::Completed)
            <p class="mt-2 text-lg font-semibold">Timer stopped</p>
            <p class="mt-1 text-xs uppercase tracking-[0.2em] text-emerald-700">Completed</p>
        @elseif ($isOverdue || $remainingSeconds === 0)
            <p class="mt-2 text-lg font-semibold">00:00:00</p>
            <p class="mt-1 text-xs uppercase tracking-[0.2em] text-rose-700">Time expired</p>
        @else
            <p class="mt-2 text-lg font-semibold" data-countdown-seconds="{{ $remainingSeconds }}">{{ $formatCountdown($remainingSeconds) }}</p>
            <p class="mt-1 text-xs uppercase tracking-[0.2em] text-amber-700">Counting down</p>
        @endif
    </div>
@endif
