@props(['status' => 'unknown'])

@php
    $normalized = strtolower((string) $status);
    $classes = match ($normalized) {
        'online', 'normal', 'running', 'connected', 'available', 'executed', 'succeeded', 'resolved' => 'bg-green-100 text-green-700',
        'warning', 'pending', 'picked_up', 'acknowledged' => 'bg-amber-100 text-amber-700',
        'error', 'failed', 'unavailable', 'timeout' => 'bg-orange-100 text-orange-700',
        'critical' => 'bg-red-100 text-red-700',
        'offline', 'cancelled', 'expired' => 'bg-slate-200 text-slate-700',
        default => 'bg-slate-100 text-slate-600',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wide {$classes}"]) }}>
    {{ strtoupper(str_replace('_', ' ', $normalized ?: 'unknown')) }}
</span>
