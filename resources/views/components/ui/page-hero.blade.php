@props([
    'kicker',
    'title',
    'description' => null,
    'tags' => [],
])

<x-ui.panel padding="p-7" class="overflow-hidden">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="workspace-kicker">{{ $kicker }}</p>
            <h1 class="mt-3 font-serif text-4xl tracking-tight text-white sm:text-5xl">{{ $title }}</h1>
            @if ($description)
                <p class="mt-3 max-w-2xl text-base leading-7 text-stone-300">{{ $description }}</p>
            @endif
        </div>

        @if (isset($actions))
            <div class="self-start lg:self-auto">
                {{ $actions }}
            </div>
        @endif
    </div>

    @if ($tags !== [])
        <div class="mt-6 flex flex-wrap gap-3">
            @foreach ($tags as $tag)
                <span class="rounded-full border border-white/10 bg-white/6 px-4 py-2 text-sm text-stone-200">{{ $tag }}</span>
            @endforeach
        </div>
    @endif
</x-ui.panel>
