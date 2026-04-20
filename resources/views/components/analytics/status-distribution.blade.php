@props([
    'chart',
    'meta',
])

<section class="rounded-[1.75rem] border border-white/10 bg-white/7 p-5">
    <p class="workspace-kicker">Status distribution</p>
    <h2 class="mt-2 font-serif text-2xl text-white">Work mix</h2>

    <div class="mt-6 flex justify-center">
        <div class="relative">
            <svg viewBox="0 0 120 120" class="h-52 w-52 -rotate-90">
                <circle cx="60" cy="60" r="45" fill="none" stroke="#e7e5e4" stroke-width="12"></circle>
                @foreach ($meta['slices'] as $slice)
                    <circle
                        cx="60"
                        cy="60"
                        r="45"
                        fill="none"
                        stroke="{{ $slice['color'] }}"
                        stroke-width="12"
                        stroke-linecap="round"
                        stroke-dasharray="{{ $slice['length'] }} {{ $meta['circumference'] }}"
                        stroke-dashoffset="{{ $slice['offset'] }}"
                    ></circle>
                @endforeach
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                <p class="text-[11px] uppercase tracking-[0.24em] text-stone-400">Total</p>
                <p class="mt-1 text-4xl font-semibold text-white">{{ $meta['total'] }}</p>
            </div>
        </div>
    </div>

    <div class="mt-6 space-y-3">
        @foreach ($chart as $slice)
            <div class="flex items-center justify-between gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                <div class="flex items-center gap-3">
                    <span class="h-3 w-3 rounded-full" style="background-color: {{ $slice['color'] }}"></span>
                    <span class="text-sm font-medium text-stone-200">{{ $slice['label'] }}</span>
                </div>
                <span class="text-sm font-semibold text-white">{{ $slice['value'] }}</span>
            </div>
        @endforeach
    </div>
</section>
