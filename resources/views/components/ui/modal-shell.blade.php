@props([
    'name',
    'title',
    'description' => null,
    'kicker' => 'New task',
    'openOnLoad' => false,
])

<div
    id="{{ $name }}"
    data-modal="{{ $name }}"
    data-open-on-load="{{ $openOnLoad ? 'true' : 'false' }}"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-stone-950/70 p-4 backdrop-blur-sm"
>
    <div class="absolute inset-0" data-modal-close="{{ $name }}"></div>
    <div class="relative max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-[2rem] border border-white/10 bg-white/10 p-8 shadow-2xl shadow-black/30 backdrop-blur-xl">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="workspace-kicker">{{ $kicker }}</p>
                <h2 class="mt-3 font-serif text-4xl tracking-tight text-white">{{ $title }}</h2>
                @if ($description)
                    <p class="mt-3 text-sm leading-6 text-stone-300">{{ $description }}</p>
                @endif
            </div>

            <button type="button" data-modal-close="{{ $name }}" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/10 bg-white/5 text-lg text-stone-200 transition hover:bg-white/10 hover:text-white">
                ×
            </button>
        </div>

        {{ $slot }}
    </div>
</div>
