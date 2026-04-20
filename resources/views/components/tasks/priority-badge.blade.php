@props([
    'priority',
])

@php
    $value = $priority instanceof \BackedEnum ? $priority->value : $priority;
    $classes = [
        'low' => 'bg-sky-100 text-sky-900',
        'medium' => 'bg-orange-100 text-orange-900',
        'high' => 'bg-rose-100 text-rose-900',
    ];
@endphp

<span {{ $attributes->class(['rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em]', $classes[$value] ?? $classes['medium']]) }}>
    {{ $value }}
</span>
