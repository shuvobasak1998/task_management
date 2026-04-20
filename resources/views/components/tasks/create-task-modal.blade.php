@props([
    'title',
    'description',
    'priorities',
    'statuses',
    'users',
])

<x-ui.modal-shell
    name="create-task-modal"
    kicker="New task"
    :title="$title"
    :description="$description"
    :open-on-load="$errors->any()"
>
    <form method="POST" action="{{ route('tasks.store') }}" class="mt-8">
        @csrf
        @include('tasks.partials.form', [
            'buttonLabel' => 'Create task',
            'showCancelLink' => false,
            'priorities' => $priorities,
            'statuses' => $statuses,
            'users' => $users,
        ])
    </form>
</x-ui.modal-shell>
