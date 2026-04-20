@props([
    'currentSection' => 'dashboard',
    'user',
])

@php
    $mobileItems = [
        ['label' => 'Dashboard', 'route' => route('dashboard'), 'section' => 'dashboard'],
        ['label' => 'Tasks', 'route' => route('tasks.index'), 'section' => 'tasks'],
    ];
@endphp

<header class="border-b border-white/10 bg-black/10 backdrop-blur-xl">
    <div class="flex items-center justify-between px-6 py-4 lg:px-8">
        <div class="flex items-center gap-3 lg:hidden">
            <x-app.logo mobile="true" />
        </div>

        <div class="ml-auto flex items-center gap-3">
            <x-app.user-profile :user="$user" class="hidden lg:block" />
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="secondary-pill">
                    Sign out
                </button>
            </form>
        </div>
    </div>

    <div class="px-6 pb-4 lg:hidden">
        <x-app.user-profile :user="$user" mobile="true" />

        <div class="mt-3 grid grid-cols-2 gap-3">
            @foreach ($mobileItems as $item)
                <a
                    href="{{ $item['route'] }}"
                    class="{{ $currentSection === $item['section'] ? 'rounded-2xl bg-white px-4 py-3 text-center text-sm font-semibold text-stone-950' : 'rounded-2xl border border-white/10 px-4 py-3 text-center text-sm text-stone-300' }}"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</header>
