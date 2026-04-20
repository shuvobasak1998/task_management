@props([
    'padding' => 'p-6',
    'variant' => 'default',
])

@php
    $variantClasses = [
        'default' => 'panel-surface',
        'soft' => 'rounded-[1.75rem] border border-white/10 bg-white/7',
    ];
@endphp

<section {{ $attributes->class([$variantClasses[$variant] ?? $variantClasses['default'], $padding]) }}>
    {{ $slot }}
</section>
