@props([
    'progress',
    'compact' => false,
])

@if ($compact)
    <div class="min-w-[140px]">
        <div class="flex items-center justify-between text-xs font-semibold uppercase tracking-[0.18em] text-stone-400">
            <span>Progress</span>
            <span class="text-white">{{ $progress }}%</span>
        </div>
        <div class="mt-3 h-2.5 overflow-hidden rounded-full bg-white/10">
            <div class="h-full rounded-full bg-gradient-to-r from-amber-400 via-orange-500 to-emerald-500" style="width: {{ $progress }}%"></div>
        </div>
    </div>
@else
    <div class="space-y-3">
        <div class="flex items-center justify-between gap-4 text-sm">
            <span class="font-medium text-stone-700">Progress</span>
            <span class="font-semibold text-stone-950">{{ $progress }}%</span>
        </div>

        <div class="h-3 overflow-hidden rounded-full bg-stone-200">
            <div class="h-full rounded-full bg-gradient-to-r from-amber-400 via-orange-500 to-emerald-500 transition-all" style="width: {{ $progress }}%"></div>
        </div>
    </div>
@endif
