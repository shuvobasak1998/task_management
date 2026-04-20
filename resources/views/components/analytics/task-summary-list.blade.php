@props([
    'kicker',
    'title',
    'tasks',
    'variant' => 'neutral',
    'emptyMessage',
])

@php
    $itemClasses = [
        'alert' => 'rounded-[1.5rem] border border-rose-400/20 bg-rose-400/10 px-5 py-4',
        'accent' => 'rounded-[1.5rem] border border-amber-300/20 bg-amber-300/10 px-5 py-4',
        'neutral' => 'rounded-[1.5rem] border border-white/10 bg-white/5 px-5 py-4',
    ];
    $metaClasses = [
        'alert' => 'text-rose-100',
        'accent' => 'text-amber-50',
        'neutral' => 'text-stone-300',
    ];
@endphp

<x-ui.panel padding="p-6">
    <x-ui.section-header :kicker="$kicker" :title="$title">
        <x-slot:meta>
            <span class="rounded-full border border-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-stone-300">
                {{ $tasks->count() }} items
            </span>
        </x-slot:meta>
    </x-ui.section-header>

    <div class="mt-6 space-y-3">
        @forelse ($tasks->take(5) as $task)
            <div class="{{ $itemClasses[$variant] ?? $itemClasses['neutral'] }}">
                <p class="text-sm font-semibold text-white">{{ $task->title }}</p>
                <p class="mt-2 text-sm {{ $metaClasses[$variant] ?? $metaClasses['neutral'] }}">
                    {{ optional($task->assignee)->name ?? 'Unassigned' }} · due {{ optional($task->due_at)?->format('M d, Y h:i A') ?? 'No due date' }}
                </p>
            </div>
        @empty
            <x-ui.empty-state :title="$emptyMessage" compact="true" />
        @endforelse
    </div>
</x-ui.panel>
