@props([
    'mobile' => false,
])

<a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3">
    <span class="{{ $mobile ? 'flex h-10 w-10 items-center justify-center rounded-2xl bg-white text-xs font-semibold tracking-[0.3em] text-stone-950' : 'flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-sm font-semibold tracking-[0.3em] text-stone-950' }}">TFS</span>
    <div>
        <p class="{{ $mobile ? 'font-serif text-lg tracking-tight text-white' : 'font-serif text-xl tracking-tight text-white' }}">TaskFlow Studio</p>
        <p class="{{ $mobile ? 'text-[11px] uppercase tracking-[0.24em] text-stone-400' : 'text-xs uppercase tracking-[0.24em] text-stone-400' }}">Technical Assessment For Qtec Solution Limited</p>
    </div>
</a>
