@props([
    'title',
    'description' => null,
    'compact' => false,
])

@php
    $wrapperClasses = $compact
        ? 'rounded-[1.5rem] border border-white/10 bg-white/5 px-5 py-8 text-center text-sm text-stone-300'
        : 'mx-auto max-w-lg';
@endphp

<div {{ $attributes->class([$wrapperClasses]) }}>
    <p class="{{ $compact ? '' : 'font-serif text-3xl text-white' }}">{{ $title }}</p>
    @if ($description)
        <p class="{{ $compact ? 'mt-2' : 'mt-3 text-sm leading-6 text-stone-400' }}">{{ $description }}</p>
    @endif
</div>
