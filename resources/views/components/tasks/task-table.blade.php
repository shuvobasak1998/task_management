@props([
    'tasks',
])

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
                @forelse ($tasks as $task)
                    <x-tasks.task-row :task="$task" />
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-14 text-center">
                            @if (isset($empty))
                                {{ $empty }}
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
