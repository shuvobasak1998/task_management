@props([
    'status',
])

@php
    $value = $status instanceof \BackedEnum ? $status->value : $status;
    $classes = [
        'pending' => 'bg-stone-100 text-stone-700',
        'in_progress' => 'bg-amber-100 text-amber-900',
        'completed' => 'bg-emerald-100 text-emerald-900',
    ];
@endphp

<span {{ $attributes->class(['inline-flex whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em]', $classes[$value] ?? $classes['pending']]) }}>
    {{ str($value)->replace('_', ' ') }}
</span>
