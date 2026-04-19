@extends('layouts.app')

@section('title', 'Dashboard | TaskFlow Studio')

@php
    $allTasks = $myTasks->concat($teamTasks);
    $totalTasks = $allTasks->count();
    $completedTasks = $allTasks->where('status', \App\Enums\TaskStatus::Completed)->count();
    $overdueTasks = $allTasks->filter(fn ($task) => $task->isOverdue())->values();
@endphp

@section('content')
    <section class="grid gap-6 xl:grid-cols-[0.95fr_1.35fr]">
        <aside class="space-y-6">
            <div class="rounded-[2rem] border border-stone-900/10 bg-white/80 p-7 shadow-[0_24px_80px_-32px_rgba(48,33,21,0.35)] backdrop-blur">
                <p class="text-sm uppercase tracking-[0.3em] text-stone-500">Create task</p>
                <h1 class="mt-3 font-serif text-3xl tracking-tight text-stone-950">Keep the whole team moving.</h1>
                <p class="mt-3 text-sm leading-6 text-stone-600">Create a task, assign ownership, and keep the team workspace organized from one dashboard.</p>

                <form method="POST" action="{{ route('tasks.store') }}" class="mt-6">
                    @csrf
                    @include('tasks.partials.form', ['buttonLabel' => 'Create task'])
                </form>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-2">
                <article class="rounded-3xl border border-stone-900/10 bg-white/75 p-5">
                    <p class="text-xs uppercase tracking-[0.24em] text-stone-500">Total tasks</p>
                    <p class="mt-3 text-3xl font-semibold text-stone-950">{{ $totalTasks }}</p>
                </article>
                <article class="rounded-3xl border border-stone-900/10 bg-white/75 p-5">
                    <p class="text-xs uppercase tracking-[0.24em] text-stone-500">My tasks</p>
                    <p class="mt-3 text-3xl font-semibold text-stone-950">{{ $myTasks->count() }}</p>
                </article>
                <article class="rounded-3xl border border-stone-900/10 bg-white/75 p-5">
                    <p class="text-xs uppercase tracking-[0.24em] text-stone-500">Completed</p>
                    <p class="mt-3 text-3xl font-semibold text-stone-950">{{ $completedTasks }}</p>
                </article>
                <article class="rounded-3xl border border-rose-200 bg-rose-50 p-5">
                    <p class="text-xs uppercase tracking-[0.24em] text-rose-600">Overdue</p>
                    <p class="mt-3 text-3xl font-semibold text-rose-900">{{ $overdueTasks->count() }}</p>
                </article>
            </div>
        </aside>

        <div class="space-y-6">
            @if ($overdueTasks->isNotEmpty())
                <section class="rounded-[2rem] border border-rose-200 bg-[linear-gradient(135deg,_rgba(254,226,226,0.92),_rgba(255,251,235,0.95))] p-7 shadow-sm">
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

            <section class="rounded-[2rem] border border-stone-900/10 bg-white/75 p-7">
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

            <section class="rounded-[2rem] border border-stone-900/10 bg-white/75 p-7">
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
