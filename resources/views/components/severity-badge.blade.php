@props(['severity' => 'info'])

@php
    $normalized = strtolower((string) $severity);
    $classes = match ($normalized) {
        'warning' => 'bg-amber-100 text-amber-700',
        'error' => 'bg-orange-100 text-orange-700',
        'critical', 'alert' => 'bg-red-100 text-red-700',
        'emerg', 'emergency' => 'bg-red-200 text-red-800',
        'notice' => 'bg-teal-100 text-teal-700',
        'debug' => 'bg-slate-200 text-slate-600',
        default => 'bg-sky-100 text-sky-700',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wide {$classes}"]) }}>
    {{ strtoupper($normalized ?: 'info') }}
</span>
