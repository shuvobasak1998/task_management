@extends('layouts.app')

@section('title', 'Dashboard | TaskFlow Studio')

@php
    $filteredTasks = $myTasks->concat($teamTasks);
    $totalTasks = $allTasks->count();
    $activeTasks = $allTasks->where('status', \App\Enums\TaskStatus::InProgress)->count();
    $completedTasks = $allTasks->where('status', \App\Enums\TaskStatus::Completed)->count();
    $overdueTasks = $allTasks->filter(fn ($task) => $task->isOverdue())->values();
    $dueSoonTasks = $allTasks->filter(fn ($task) => $task->isDueSoon())->values();
@endphp

@section('content')
    <section class="grid gap-6 xl:grid-cols-[0.95fr_1.35fr]">
        <aside class="space-y-6 xl:sticky xl:top-6 xl:self-start">
            <div class="panel-surface bg-white/80 p-7">
                <p class="text-sm uppercase tracking-[0.3em] text-stone-500">Create task</p>
                <h1 class="mt-3 font-serif text-3xl tracking-tight text-stone-950">Keep the whole team moving.</h1>
                <p class="mt-3 text-sm leading-6 text-stone-600">Create a task, assign ownership, and keep the team workspace organized from one dashboard.</p>

                <form method="POST" action="{{ route('tasks.store') }}" class="mt-6">
                    @csrf
                    @include('tasks.partials.form', ['buttonLabel' => 'Create task'])
                </form>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-2">
                <article class="metric-card">
                    <p class="text-xs uppercase tracking-[0.24em] text-stone-500">Total tasks</p>
                    <p class="mt-3 text-3xl font-semibold text-stone-950">{{ $totalTasks }}</p>
                </article>
                <article class="metric-card">
                    <p class="text-xs uppercase tracking-[0.24em] text-stone-500">My tasks</p>
                    <p class="mt-3 text-3xl font-semibold text-stone-950">{{ $allTasks->filter(fn ($task) => $task->created_by === auth()->id() || $task->assigned_to === auth()->id())->count() }}</p>
                </article>
                <article class="metric-card">
                    <p class="text-xs uppercase tracking-[0.24em] text-stone-500">Active now</p>
                    <p class="mt-3 text-3xl font-semibold text-stone-950">{{ $activeTasks }}</p>
                </article>
                <article class="metric-card">
                    <p class="text-xs uppercase tracking-[0.24em] text-stone-500">Completed</p>
                    <p class="mt-3 text-3xl font-semibold text-stone-950">{{ $completedTasks }}</p>
                </article>
                <article class="metric-card-alert">
                    <p class="text-xs uppercase tracking-[0.24em] text-rose-600">Overdue</p>
                    <p class="mt-3 text-3xl font-semibold text-rose-900">{{ $overdueTasks->count() }}</p>
                </article>
                <article class="metric-card-accent">
                    <p class="text-xs uppercase tracking-[0.24em] text-amber-700">Due soon</p>
                    <p class="mt-3 text-3xl font-semibold text-amber-900">{{ $dueSoonTasks->count() }}</p>
                </article>
            </div>
        </aside>

        <div class="space-y-6">
            <section class="panel-surface p-7">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm uppercase tracking-[0.24em] text-stone-500">Find work fast</p>
                        <h2 class="mt-2 font-serif text-2xl text-stone-950">Search and filter the workspace</h2>
                    </div>
                    <span class="rounded-full border border-stone-900/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-stone-600">
                        {{ $filteredTasks->count() }} matches
                    </span>
                </div>

                <form method="GET" action="{{ route('dashboard') }}" class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                    <div class="xl:col-span-2">
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

                    <div class="md:col-span-2 xl:col-span-5 flex flex-wrap items-center gap-3">
                        <label class="inline-flex items-center gap-3 rounded-full border border-stone-900/10 bg-stone-50 px-4 py-2 text-sm text-stone-700">
                            <input type="checkbox" name="overdue" value="1" @checked($filters['overdue']) class="h-4 w-4 rounded border-stone-300 text-rose-600 focus:ring-rose-400">
                            Overdue only
                        </label>

                        <button type="submit" class="primary-pill">
                            Apply filters
                        </button>

                        <a href="{{ route('dashboard') }}" class="secondary-pill">
                            Reset
                        </a>
                    </div>
                </form>
            </section>

            @if ($overdueTasks->isNotEmpty())
                <section class="panel-surface border-rose-200 bg-[linear-gradient(135deg,_rgba(254,226,226,0.92),_rgba(255,251,235,0.95))] p-7 shadow-sm">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <p class="text-sm uppercase tracking-[0.24em] text-rose-700">Needs attention</p>
                            <h2 class="mt-2 font-serif text-2xl text-rose-950">Some tasks have run out of time.</h2>
                            <p class="mt-3 max-w-2xl text-sm leading-6 text-rose-800">
                                These tasks are past their expected finish window. Bring them forward, reassign them, or update progress to keep delivery honest.
                            </p>
                        </div>
                        <span class="rounded-full bg-rose-900 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-rose-50">
                            {{ $overdueTasks->count() }} overdue
                        </span>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-3">
                        @foreach ($overdueTasks->take(4) as $task)
                            <span class="rounded-full border border-rose-200 bg-white/80 px-4 py-2 text-sm font-medium text-rose-900">
                                {{ $task->title }}
                            </span>
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="panel-surface p-7">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.24em] text-stone-500">My work</p>
                        <h2 class="mt-2 font-serif text-2xl text-stone-950">Tasks connected to you</h2>
                    </div>
                    <span class="rounded-full bg-stone-950 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-stone-50">{{ $myTasks->count() }} items</span>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse ($myTasks as $task)
                        @include('tasks.partials.task-card', ['task' => $task])
                    @empty
                        <div class="rounded-3xl border border-dashed border-stone-300 bg-stone-50 p-8 text-sm leading-6 text-stone-600">
                            No tasks are assigned to you yet. Create the first one to start shaping the workspace.
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="panel-surface p-7">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.24em] text-stone-500">Team board</p>
                        <h2 class="mt-2 font-serif text-2xl text-stone-950">Shared workspace activity</h2>
                    </div>
                    <span class="rounded-full border border-stone-900/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-stone-600">{{ $teamTasks->count() }} items</span>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse ($teamTasks as $task)
                        @include('tasks.partials.task-card', ['task' => $task])
                    @empty
                        <div class="rounded-3xl border border-dashed border-stone-300 bg-stone-50 p-8 text-sm leading-6 text-stone-600">
                            Once teammates add more work, shared tasks will appear here.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </section>
@endsection
