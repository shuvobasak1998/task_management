@php
    /** @var \App\Models\Task|null $task */
    $task ??= null;
    $buttonLabel ??= 'Save task';
@endphp

<div class="grid gap-5 md:grid-cols-2">
    <div class="space-y-2 md:col-span-2">
        <label for="title" class="text-sm font-medium text-stone-700">Task title</label>
        <input id="title" type="text" name="title" value="{{ old('title', $task?->title) }}" required class="w-full rounded-2xl border border-stone-900/10 bg-white px-4 py-3 text-stone-900 outline-none transition focus:border-stone-950" placeholder="Launch sprint planning board">
        @error('title')
            <p class="text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2 md:col-span-2">
        <label for="description" class="text-sm font-medium text-stone-700">Description</label>
        <textarea id="description" name="description" rows="4" class="w-full rounded-2xl border border-stone-900/10 bg-white px-4 py-3 text-stone-900 outline-none transition focus:border-stone-950" placeholder="Capture what success looks like, key notes, or any helpful context.">{{ old('description', $task?->description) }}</textarea>
        @error('description')
            <p class="text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2">
        <label for="priority" class="text-sm font-medium text-stone-700">Priority</label>
        <select id="priority" name="priority" class="w-full rounded-2xl border border-stone-900/10 bg-white px-4 py-3 text-stone-900 outline-none transition focus:border-stone-950">
            @foreach ($priorities as $priority)
                <option value="{{ $priority->value }}" @selected(old('priority', $task?->priority?->value ?? \App\Enums\TaskPriority::Medium->value) === $priority->value)>
                    {{ str($priority->value)->replace('_', ' ')->title() }}
                </option>
            @endforeach
        </select>
        @error('priority')
            <p class="text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2">
        <label for="status" class="text-sm font-medium text-stone-700">Status</label>
        <select id="status" name="status" class="w-full rounded-2xl border border-stone-900/10 bg-white px-4 py-3 text-stone-900 outline-none transition focus:border-stone-950">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $task?->status?->value ?? \App\Enums\TaskStatus::Pending->value) === $status->value)>
                    {{ str($status->value)->replace('_', ' ')->title() }}
                </option>
            @endforeach
        </select>
        @error('status')
            <p class="text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2">
        <label for="assigned_to" class="text-sm font-medium text-stone-700">Assign to</label>
        <select id="assigned_to" name="assigned_to" class="w-full rounded-2xl border border-stone-900/10 bg-white px-4 py-3 text-stone-900 outline-none transition focus:border-stone-950">
            <option value="">Unassigned for now</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected((string) old('assigned_to', $task?->assigned_to) === (string) $user->id)>
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
        @error('assigned_to')
            <p class="text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2">
        <label for="estimated_minutes" class="text-sm font-medium text-stone-700">Estimated minutes</label>
        <input id="estimated_minutes" type="number" min="1" max="10080" name="estimated_minutes" value="{{ old('estimated_minutes', $task?->estimated_minutes ?? 60) }}" required class="w-full rounded-2xl border border-stone-900/10 bg-white px-4 py-3 text-stone-900 outline-none transition focus:border-stone-950">
        @error('estimated_minutes')
            <p class="text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2">
        <label for="due_at" class="text-sm font-medium text-stone-700">Due at</label>
        <input id="due_at" type="datetime-local" name="due_at" value="{{ old('due_at', optional($task?->due_at)->format('Y-m-d\TH:i')) }}" class="w-full rounded-2xl border border-stone-900/10 bg-white px-4 py-3 text-stone-900 outline-none transition focus:border-stone-950">
        @error('due_at')
            <p class="text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    @if ($task)
        <div class="space-y-2">
            <label for="progress_percent" class="text-sm font-medium text-stone-700">Progress percent</label>
            <input id="progress_percent" type="number" min="0" max="100" name="progress_percent" value="{{ old('progress_percent', $task->progress_percent) }}" required class="w-full rounded-2xl border border-stone-900/10 bg-white px-4 py-3 text-stone-900 outline-none transition focus:border-stone-950">
            @error('progress_percent')
                <p class="text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    @endif
</div>

<div class="mt-6 flex flex-wrap items-center gap-3">
    <button type="submit" class="inline-flex items-center rounded-full bg-stone-950 px-5 py-3 text-sm font-semibold text-stone-50 transition hover:bg-stone-800">
        {{ $buttonLabel }}
    </button>

    <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-full border border-stone-900/10 bg-white px-5 py-3 text-sm font-medium text-stone-700 transition hover:border-stone-900/30 hover:text-stone-950">
        Cancel
    </a>
</div>
