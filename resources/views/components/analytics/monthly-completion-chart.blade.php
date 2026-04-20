@props([
    'points',
    'peak',
])

<x-ui.panel padding="p-6" class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
    <section>
        <x-ui.section-header kicker="Analytics" title="Monthly completed ratio">
            <x-slot:meta>
                <span class="rounded-full border border-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-stone-300">Last 6 months</span>
            </x-slot:meta>
        </x-ui.section-header>

        <div class="mt-8">
            <div class="flex h-56 items-end justify-between gap-3">
                @foreach ($points as $point)
                    @php($height = max(16, ($point['ratio'] / $peak) * 180))
                    <div class="flex flex-1 flex-col items-center gap-3">
                        <div class="flex h-48 w-full items-end justify-center">
                            <div class="chart-bar w-full max-w-14 rounded-t-[1.25rem] bg-gradient-to-t from-stone-950 via-stone-700 to-amber-400" style="height: {{ $height }}px">
                                <span class="mb-3 block text-center text-xs font-semibold text-white">{{ $point['ratio'] }}%</span>
                            </div>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-semibold text-white">{{ $point['label'] }}</p>
                            <p class="text-[11px] uppercase tracking-[0.18em] text-stone-400">{{ $point['completed'] }}/{{ max(1, $point['created']) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @if (isset($side))
        {{ $side }}
    @endif
</x-ui.panel>
