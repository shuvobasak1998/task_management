@props([
    'task',
    'compact' => false,
])

@php
    $baseButton = $compact
        ? 'table-action'
        : 'inline-flex items-center rounded-full border border-stone-900/10 px-4 py-2 text-sm font-medium text-stone-700 transition hover:border-stone-900/30 hover:text-stone-950';
    $primaryButton = $compact
        ? 'table-action table-action-primary'
        : 'inline-flex items-center rounded-full bg-stone-950 px-4 py-2 text-sm font-medium text-stone-50 transition hover:bg-stone-800';
    $dangerButton = $compact
        ? 'table-action table-action-danger'
        : 'inline-flex items-center rounded-full border border-rose-200 px-4 py-2 text-sm font-medium text-rose-700 transition hover:bg-rose-50';
    $reopenButton = $compact
        ? 'table-action'
        : 'inline-flex items-center rounded-full border border-amber-200 bg-amber-50 px-4 py-2 text-sm font-medium text-amber-900 transition hover:bg-amber-100';
@endphp

<div {{ $attributes->class([$compact ? 'flex min-w-[190px] flex-wrap gap-2' : 'flex flex-wrap gap-3 lg:max-w-xs lg:justify-end']) }}>
    @can('update', $task)
        @if ($task->status === \App\Enums\TaskStatus::Completed)
            <form method="POST" action="{{ route('tasks.progress', $task) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="target_progress" value="90">
                <button type="submit" class="{{ $reopenButton }}">
                    {{ $compact ? 'Reopen' : 'Reopen at 90%' }}
                </button>
            </form>
        @else
            <form method="POST" action="{{ route('tasks.progress', $task) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="delta" value="10">
                <button type="submit" class="{{ $baseButton }}">
                    +10%
                </button>
            </form>

            @unless ($compact)
                <form method="POST" action="{{ route('tasks.progress', $task) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="delta" value="25">
                    <button type="submit" class="{{ $baseButton }}">
                        +25%
                    </button>
                </form>
            @endunless

            <form method="POST" action="{{ route('tasks.progress', $task) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="target_progress" value="100">
                <button type="submit" class="{{ $primaryButton }}">
                    Complete
                </button>
            </form>
        @endif

        <a href="{{ route('tasks.edit', $task) }}" class="{{ $baseButton }}">
            Edit
        </a>
    @endcan

    @can('delete', $task)
        <form method="POST" action="{{ route('tasks.destroy', $task) }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="{{ $dangerButton }}">
                Delete
            </button>
        </form>
    @endcan
</div>
