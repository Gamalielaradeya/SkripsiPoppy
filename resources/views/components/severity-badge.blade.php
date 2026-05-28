@props(['severity' => 'info'])

@php
    $normalized = strtolower((string) $severity);
    $classes = match ($normalized) {
        'warning' => 'bg-amber-100 text-amber-700',
        'error' => 'bg-orange-100 text-orange-700',
        'critical' => 'bg-red-100 text-red-700',
        default => 'bg-sky-100 text-sky-700',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium uppercase {$classes}"]) }}>
    {{ $normalized ?: 'info' }}
</span>
