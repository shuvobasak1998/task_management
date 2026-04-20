@php
    /** @var \App\Models\Task|null $task */
    $task ??= null;
    $buttonLabel ??= 'Save task';
    $showCancelLink ??= true;
@endphp

<div class="grid gap-5 md:grid-cols-2">
    <div class="space-y-2 md:col-span-2">
        <label for="title" class="text-sm font-medium text-stone-200">Task title</label>
        <input id="title" type="text" name="title" value="{{ old('title', $task?->title) }}" required class="soft-input" placeholder="Launch sprint planning board">
        @error('title')
            <p class="text-sm text-rose-300">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2 md:col-span-2">
        <label for="description" class="text-sm font-medium text-stone-200">Description</label>
        <textarea id="description" name="description" rows="4" class="soft-input" placeholder="Capture what success looks like, key notes, or any helpful context.">{{ old('description', $task?->description) }}</textarea>
        @error('description')
            <p class="text-sm text-rose-300">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2">
        <label for="priority" class="text-sm font-medium text-stone-200">Priority</label>
        <select id="priority" name="priority" class="soft-input">
            @foreach ($priorities as $priority)
                <option value="{{ $priority->value }}" @selected(old('priority', $task?->priority?->value ?? \App\Enums\TaskPriority::Medium->value) === $priority->value)>
                    {{ str($priority->value)->replace('_', ' ')->title() }}
                </option>
            @endforeach
        </select>
        @error('priority')
            <p class="text-sm text-rose-300">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2">
        <label for="status" class="text-sm font-medium text-stone-200">Status</label>
        <select id="status" name="status" class="soft-input">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $task?->status?->value ?? \App\Enums\TaskStatus::Pending->value) === $status->value)>
                    {{ str($status->value)->replace('_', ' ')->title() }}
                </option>
            @endforeach
        </select>
        @error('status')
            <p class="text-sm text-rose-300">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2">
        <label for="assigned_to" class="text-sm font-medium text-stone-200">Assign to</label>
        <select id="assigned_to" name="assigned_to" class="soft-input">
            <option value="">Unassigned for now</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected((string) old('assigned_to', $task?->assigned_to) === (string) $user->id)>
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
        @error('assigned_to')
            <p class="text-sm text-rose-300">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2">
        <label for="due_at" class="text-sm font-medium text-stone-200">Due at</label>
        <div class="relative">
            <input
                id="due_at"
                type="datetime-local"
                name="due_at"
                value="{{ old('due_at', optional($task?->due_at)->format('Y-m-d\TH:i')) }}"
                class="soft-input pr-14"
            >
            <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-amber-300/80">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 2v3m8-3v3M4 9h16M6.5 5h11A1.5 1.5 0 0 1 19 6.5v11A1.5 1.5 0 0 1 17.5 19h-11A1.5 1.5 0 0 1 5 17.5v-11A1.5 1.5 0 0 1 6.5 5Zm5.5 7v3l2 1.5" />
                </svg>
            </div>
        </div>
        <p class="text-xs text-stone-400">Pick both date and time from the calendar.</p>
        @error('due_at')
            <p class="text-sm text-rose-300">{{ $message }}</p>
        @enderror
    </div>

    @if ($task)
        <div class="space-y-2">
            <label for="progress_percent" class="text-sm font-medium text-stone-200">Progress percent</label>
            <input id="progress_percent" type="number" min="0" max="100" name="progress_percent" value="{{ old('progress_percent', $task->progress_percent) }}" required class="soft-input">
            @error('progress_percent')
                <p class="text-sm text-rose-300">{{ $message }}</p>
            @enderror
        </div>
    @endif
</div>

<div class="mt-6 flex flex-wrap items-center gap-3">
    <button type="submit" class="primary-pill">
        {{ $buttonLabel }}
    </button>

    @if ($showCancelLink)
        <a href="{{ url()->previous() }}" class="secondary-pill">
            Cancel
        </a>
    @endif
</div>
