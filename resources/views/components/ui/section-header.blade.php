@props([
    'kicker',
    'title',
])

<div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
    <div>
        <p class="workspace-kicker">{{ $kicker }}</p>
        <h2 class="mt-2 font-serif text-2xl text-white">{{ $title }}</h2>
    </div>

    @if (isset($meta))
        {{ $meta }}
    @endif
</div>
