@props([
    'variant' => 'secondary',
    'disabled' => false,
    'href' => null,
])

@php
    $base = 'inline-flex items-center justify-center rounded-md px-3 py-2 text-sm font-medium transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2';
    $variants = [
        'primary' => 'bg-slate-900 text-white hover:bg-slate-800 focus-visible:outline-slate-700',
        'secondary' => 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 focus-visible:outline-slate-500',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus-visible:outline-red-600',
        'ghost' => 'text-slate-600 hover:bg-slate-100 focus-visible:outline-slate-500',
    ];
    $classes = $base.' '.($variants[$variant] ?? $variants['secondary']);
    $disabledClasses = 'pointer-events-none cursor-not-allowed opacity-50';
@endphp

@if ($href && ! $disabled)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="button" @disabled($disabled) {{ $attributes->merge(['class' => $classes.' '.($disabled ? $disabledClasses : '')]) }}>
        {{ $slot }}
    </button>
@endif
