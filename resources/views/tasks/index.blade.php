@extends('layouts.app')

@section('title', 'Dashboard | TaskFlow Studio')

@php
    $totalTasks = $allTasks->count();
    $activeTasks = $allTasks->where('status', \App\Enums\TaskStatus::InProgress)->count();
    $completedTasks = $allTasks->where('status', \App\Enums\TaskStatus::Completed)->count();
    $overdueTasks = $allTasks->filter(fn ($task) => $task->isOverdue())->values();
    $dueSoonTasks = $allTasks->filter(fn ($task) => $task->isDueSoon())->values();
    $distributionTotal = max(1, $totalTasks);
    $circumference = 282.74;
    $distributionSlices = collect($distributionChart)->values()->reduce(function ($carry, $item) use ($distributionTotal, $circumference) {
        $length = ($item['value'] / $distributionTotal) * $circumference;
        $carry[] = [
            ...$item,
            'length' => $length,
            'offset' => -collect($carry)->sum('length'),
        ];

        return $carry;
    }, []);
    $chartPeak = max(1, collect($monthlyCompletionChart)->max('ratio'));
@endphp

@section('content')
    <div class="space-y-8">
        <section class="grid gap-6 xl:grid-cols-[1.25fr_0.95fr]">
            <div class="space-y-6">
                <div class="panel-surface overflow-hidden p-7">
                    <div class="absolute"></div>
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <p class="workspace-kicker">Home</p>
                            <h1 class="mt-3 font-serif text-4xl tracking-tight text-white sm:text-5xl">A smarter view of team execution.</h1>
                            <p class="mt-3 max-w-2xl text-base leading-7 text-stone-300">
                                Your workspace is live at first glance: priorities, deadlines, progress, and team movement all in one place.
                            </p>
                        </div>

                        <button type="button" data-modal-trigger="create-task-modal" class="primary-pill self-start lg:self-auto">
                            Create task
                        </button>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <span class="rounded-full border border-white/10 bg-white/6 px-4 py-2 text-sm text-stone-200">Workspace table</span>
                        <span class="rounded-full border border-white/10 bg-white/6 px-4 py-2 text-sm text-stone-200">Live timers</span>
                        <span class="rounded-full border border-white/10 bg-white/6 px-4 py-2 text-sm text-stone-200">Team analytics</span>
                    </div>
                </div>

                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="workspace-kicker">Workspace focus</p>
                        <h2 class="mt-2 font-serif text-3xl tracking-tight text-white">See the whole workspace at a glance.</h2>
                        <p class="mt-3 max-w-2xl text-base leading-7 text-stone-300">
                            Track delivery, scan deadlines, and move work forward from one modern workspace.
                        </p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    <article class="metric-card">
                        <p class="text-xs uppercase tracking-[0.24em] text-stone-400">Total tasks</p>
                        <p class="mt-3 text-3xl font-semibold text-white">{{ $totalTasks }}</p>
                    </article>
                    <article class="metric-card">
                        <p class="text-xs uppercase tracking-[0.24em] text-stone-400">My tasks</p>
                        <p class="mt-3 text-3xl font-semibold text-white">{{ $myTasksCount }}</p>
                    </article>
                    <article class="metric-card">
                        <p class="text-xs uppercase tracking-[0.24em] text-stone-400">Active now</p>
                        <p class="mt-3 text-3xl font-semibold text-white">{{ $activeTasks }}</p>
                    </article>
                    <article class="metric-card">
                        <p class="text-xs uppercase tracking-[0.24em] text-stone-400">Completed</p>
                        <p class="mt-3 text-3xl font-semibold text-white">{{ $completedTasks }}</p>
                    </article>
                    <article class="metric-card-alert">
                        <p class="text-xs uppercase tracking-[0.24em] text-rose-200">Overdue</p>
                        <p class="mt-3 text-3xl font-semibold text-white">{{ $overdueTasks->count() }}</p>
                    </article>
                    <article class="metric-card-accent">
                        <p class="text-xs uppercase tracking-[0.24em] text-amber-100">Due soon</p>
                        <p class="mt-3 text-3xl font-semibold text-white">{{ $dueSoonTasks->count() }}</p>
                    </article>
                </div>
            </div>

            <div class="panel-surface grid gap-6 p-6 lg:grid-cols-[1.15fr_0.85fr]">
                <section>
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="workspace-kicker">Analytics</p>
                            <h2 class="mt-2 font-serif text-2xl text-white">Monthly completed ratio</h2>
                        </div>
                        <span class="rounded-full border border-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-stone-300">Last 6 months</span>
                    </div>

                    <div class="mt-8">
                        <div class="flex h-56 items-end justify-between gap-3">
                            @foreach ($monthlyCompletionChart as $point)
                                @php
                                    $height = max(16, ($point['ratio'] / $chartPeak) * 180);
                                @endphp
                                <div class="flex flex-1 flex-col items-center gap-3">
                                    <div class="flex h-48 w-full items-end justify-center">
                                        <div class="chart-bar w-full max-w-14 rounded-t-[1.25rem] bg-gradient-to-t from-stone-950 via-stone-700 to-amber-400" style="height: {{ $height }}px">
                                            <span class="mb-3 block text-center text-xs font-semibold text-white">{{ $point['ratio'] }}%</span>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-sm font-semibold text-white">{{ $point['label'] }}</p>
                                        <p class="text-[11px] uppercase tracking-[0.18em] text-stone-400">{{ $point['completed'] }}/{{ max(1, $point['created']) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                <section class="rounded-[1.75rem] border border-white/10 bg-white/7 p-5">
                    <p class="workspace-kicker">Status distribution</p>
                    <h2 class="mt-2 font-serif text-2xl text-white">Work mix</h2>

                    <div class="mt-6 flex justify-center">
                        <div class="relative">
                            <svg viewBox="0 0 120 120" class="h-52 w-52 -rotate-90">
                                <circle cx="60" cy="60" r="45" fill="none" stroke="#e7e5e4" stroke-width="12"></circle>
                                @foreach ($distributionSlices as $slice)
                                    <circle
                                        cx="60"
                                        cy="60"
                                        r="45"
                                        fill="none"
                                        stroke="{{ $slice['color'] }}"
                                        stroke-width="12"
                                        stroke-linecap="round"
                                        stroke-dasharray="{{ $slice['length'] }} {{ $circumference }}"
                                        stroke-dashoffset="{{ $slice['offset'] }}"
                                    ></circle>
                                @endforeach
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                                <p class="text-[11px] uppercase tracking-[0.24em] text-stone-400">Total</p>
                                <p class="mt-1 text-4xl font-semibold text-white">{{ $totalTasks }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        @foreach ($distributionChart as $slice)
                            <div class="flex items-center justify-between gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="h-3 w-3 rounded-full" style="background-color: {{ $slice['color'] }}"></span>
                                    <span class="text-sm font-medium text-stone-200">{{ $slice['label'] }}</span>
                                </div>
                                <span class="text-sm font-semibold text-white">{{ $slice['value'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>
        </section>

        <section class="panel-surface p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="workspace-kicker">Workspace</p>
                    <h2 class="mt-2 font-serif text-2xl text-white">Search and filter the workspace</h2>
                </div>
                <span class="rounded-full border border-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-stone-300">
                    {{ $filteredTasks->count() }} visible tasks
                </span>
            </div>

            <form method="GET" action="{{ route('dashboard') }}" class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-[1.4fr_0.8fr_0.8fr_0.9fr_auto]">
                <div>
                    <label for="search" class="sr-only">Search tasks</label>
                    <input id="search" type="text" name="search" value="{{ $filters['search'] }}" class="soft-input" placeholder="Search title or description">
                </div>

                <div>
                    <label for="status_filter" class="sr-only">Status</label>
                    <select id="status_filter" name="status" class="soft-input">
                        <option value="">All statuses</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected($filters['status'] === $status->value)>
                                {{ str($status->value)->replace('_', ' ')->title() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="priority_filter" class="sr-only">Priority</label>
                    <select id="priority_filter" name="priority" class="soft-input">
                        <option value="">All priorities</option>
                        @foreach ($priorities as $priority)
                            <option value="{{ $priority->value }}" @selected($filters['priority'] === $priority->value)>
                                {{ str($priority->value)->replace('_', ' ')->title() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="assigned_filter" class="sr-only">Assignee</label>
                    <select id="assigned_filter" name="assigned_to" class="soft-input">
                        <option value="">All assignees</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected((string) $filters['assigned_to'] === (string) $user->id)>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-wrap items-center gap-3 xl:justify-end">
                    <label class="inline-flex items-center gap-3 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-stone-200">
                        <input type="checkbox" name="overdue" value="1" @checked($filters['overdue']) class="h-4 w-4 rounded border-stone-300 text-rose-600 focus:ring-rose-400">
                        Overdue only
                    </label>

                    <button type="submit" class="primary-pill">
                        Apply
                    </button>

                    <a href="{{ route('dashboard') }}" class="secondary-pill">
                        Reset
                    </a>
                </div>
            </form>

            <div class="mt-8 overflow-hidden rounded-[1.75rem] border border-white/10 bg-white/6">
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse">
                        <thead class="bg-black/20 text-left text-xs uppercase tracking-[0.2em] text-stone-300">
                            <tr>
                                <th class="px-4 py-4 font-medium">Task</th>
                                <th class="px-4 py-4 font-medium">Status</th>
                                <th class="px-4 py-4 font-medium">Progress</th>
                                <th class="px-4 py-4 font-medium">Priority</th>
                                <th class="px-4 py-4 font-medium">Ownership</th>
                                <th class="px-4 py-4 font-medium">Due</th>
                                <th class="px-4 py-4 font-medium">Timer</th>
                                <th class="px-4 py-4 font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-transparent">
                            @forelse ($filteredTasks as $task)
                                @include('tasks.partials.table-row', ['task' => $task])
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-14 text-center">
                                        <div class="mx-auto max-w-lg">
                                            <p class="font-serif text-3xl text-white">No tasks match these filters.</p>
                                            <p class="mt-3 text-sm leading-6 text-stone-400">Try resetting the workspace filters or create a new task from the sidebar or page action.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <div
        id="create-task-modal"
        data-modal="create-task-modal"
        data-open-on-load="{{ $errors->any() ? 'true' : 'false' }}"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-stone-950/70 p-4 backdrop-blur-sm"
    >
        <div class="absolute inset-0" data-modal-close="create-task-modal"></div>
        <div class="relative max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-[2rem] border border-white/10 bg-white/10 p-8 shadow-2xl shadow-black/30 backdrop-blur-xl">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="workspace-kicker">New task</p>
                    <h2 class="mt-3 font-serif text-4xl tracking-tight text-white">Create a task without leaving the workspace</h2>
                    <p class="mt-3 text-sm leading-6 text-stone-300">Capture ownership, timing, and priority in one focused modal.</p>
                </div>

                <button type="button" data-modal-close="create-task-modal" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/10 bg-white/5 text-lg text-stone-200 transition hover:bg-white/10 hover:text-white">
                    ×
                </button>
            </div>

            <form method="POST" action="{{ route('tasks.store') }}" class="mt-8">
                @csrf
                @include('tasks.partials.form', ['buttonLabel' => 'Create task', 'showCancelLink' => false])
            </form>
        </div>
    </div>
@endsection
