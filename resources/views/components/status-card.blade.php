@props([
    'title',
    'value' => '-',
    'status' => 'unknown',
    'description' => '',
    'href' => null,
    'meta' => null,
])

@php
    $classes = 'block rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-slate-300 hover:shadow';
@endphp

@if ($href)
<a href="{{ $href }}" class="{{ $classes }}">
@else
<section class="{{ $classes }}">
@endif
    <div class="flex items-start justify-between gap-4">
        <h2 class="text-sm font-semibold text-slate-600">{{ $title }}</h2>
        <x-status-badge :status="$status" />
    </div>
    <div class="mt-3 text-2xl font-semibold text-slate-950">{{ $value }}</div>
    @if ($description)
        <p class="mt-2 text-sm text-slate-500">{{ $description }}</p>
    @endif
    @if ($meta)
        <p class="mt-4 border-t border-slate-100 pt-3 text-xs font-medium uppercase tracking-wide text-slate-400">{{ $meta }}</p>
    @endif
@if ($href)
</a>
@else
</section>
@endif
