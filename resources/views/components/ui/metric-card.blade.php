@props([
    'label',
    'value',
    'variant' => 'default',
])

@php
    $classes = [
        'default' => 'metric-card',
        'alert' => 'metric-card-alert',
        'accent' => 'metric-card-accent',
    ];
    $labelClasses = [
        'default' => 'text-stone-400',
        'alert' => 'text-rose-200',
        'accent' => 'text-amber-100',
    ];
@endphp

<article {{ $attributes->class([$classes[$variant] ?? $classes['default']]) }}>
    <p class="text-xs uppercase tracking-[0.24em] {{ $labelClasses[$variant] ?? $labelClasses['default'] }}">{{ $label }}</p>
    <p class="mt-3 text-3xl font-semibold text-white">{{ $value }}</p>
</article>
