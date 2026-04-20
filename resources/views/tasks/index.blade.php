@extends('layouts.app')

@section('title', 'Tasks | TaskFlow Studio')
@section('current_section', 'tasks')
@section('supports_create_task', 'true')

@section('content')
    <div class="space-y-8">
        <x-ui.page-hero
            kicker="Tasks"
            title="Workspace for active delivery."
            description="Focus only on execution here: search tasks, track owners, update progress, and create new work fast."
            :tags="['Workspace table', 'Filters and search', 'Progress updates']"
        >
            <x-slot:actions>
                <button type="button" data-modal-trigger="create-task-modal" class="primary-pill">
                    Create task
                </button>
            </x-slot:actions>
        </x-ui.page-hero>

        <x-ui.panel padding="p-6">
            <x-ui.section-header kicker="Workspace" title="Search and filter the workspace">
                <x-slot:meta>
                    <span class="rounded-full border border-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-stone-300">
                        {{ $filteredTaskCount }} visible tasks
                    </span>
                </x-slot:meta>
            </x-ui.section-header>

            <x-tasks.filter-bar
                :action="route('tasks.index')"
                :filters="$filters"
                :statuses="$statuses"
                :priorities="$priorities"
                :users="$users"
            />

            <x-tasks.task-table :tasks="$filteredTasks">
                <x-slot:empty>
                    <x-ui.empty-state
                        title="No tasks match these filters."
                        description="Try resetting the workspace filters or create a new task from the sidebar or page action."
                    />
                </x-slot:empty>
            </x-tasks.task-table>
        </x-ui.panel>
    </div>

    <x-tasks.create-task-modal
        title="Create a task without leaving the workspace"
        description="Capture ownership, timing, and priority in one focused modal."
        :priorities="$priorities"
        :statuses="$statuses"
        :users="$users"
    />
@endsection
