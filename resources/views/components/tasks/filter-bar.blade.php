@props([
    'action',
    'filters',
    'statuses',
    'priorities',
    'users',
])

<form method="GET" action="{{ $action }}" class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-[1.4fr_0.8fr_0.8fr_0.9fr_auto]">
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

        <a href="{{ $action }}" class="secondary-pill">
            Reset
        </a>
    </div>
</form>
