@props([
    'user',
    'mobile' => false,
])

<div {{ $attributes->class([$mobile ? 'rounded-[1.5rem] border border-white/10 bg-white/6 px-4 py-3' : 'rounded-[1.5rem] border border-white/10 bg-white/6 px-4 py-3 text-right']) }}>
    <p class="text-sm font-semibold text-white">{{ $user->name }}</p>
    <p class="mt-1 text-xs uppercase tracking-[0.22em] text-stone-400">{{ $user->email }}</p>
</div>
