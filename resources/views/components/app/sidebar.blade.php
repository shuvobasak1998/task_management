@props([
    'currentSection' => 'dashboard',
    'supportsCreateTask' => false,
])

@php
    $navItems = [
        [
            'label' => 'Dashboard',
            'route' => route('dashboard'),
            'section' => 'dashboard',
            'badge' => 'Live',
        ],
        [
            'label' => 'Tasks',
            'route' => route('tasks.index'),
            'section' => 'tasks',
            'badge' => 'Workspace',
        ],
    ];
@endphp

<aside class="hidden w-80 shrink-0 border-r border-white/10 bg-black/10 p-6 backdrop-blur-xl lg:flex lg:flex-col">
    <x-app.logo />

    <x-ui.panel padding="p-5" class="mt-10">
        <p class="workspace-kicker">Workspace</p>
        <nav class="mt-5 space-y-2">
            @foreach ($navItems as $item)
                @php($active = $currentSection === $item['section'])
                <a
                    href="{{ $item['route'] }}"
                    class="{{ $active ? 'flex items-center justify-between rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-stone-950' : 'flex items-center justify-between rounded-2xl border border-white/10 px-4 py-3 text-sm text-stone-300 transition hover:border-white/20 hover:bg-white/5 hover:text-white' }}"
                >
                    {{ $item['label'] }}
                    <span class="rounded-full px-2 py-1 text-[10px] uppercase tracking-[0.2em] {{ $active ? 'bg-stone-950/10 text-stone-700' : 'bg-white/10 text-stone-300' }}">{{ $item['badge'] }}</span>
                </a>
            @endforeach
        </nav>
    </x-ui.panel>

    <x-ui.panel padding="p-5" class="mt-6 text-stone-50">
        <p class="workspace-kicker">Quick action</p>
        <p class="mt-3 font-serif text-2xl">Create and assign the next task.</p>
        @if ($supportsCreateTask)
            <button type="button" data-modal-trigger="create-task-modal" class="primary-pill mt-5">
                New task
            </button>
        @else
            <a href="{{ route('tasks.index') }}" class="primary-pill mt-5 inline-flex">
                Open tasks
            </a>
        @endif
    </x-ui.panel>
</aside>
