@props([
    'title',
    'value' => '-',
    'status' => 'unknown',
    'description' => '',
])

<section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
    <div class="flex items-start justify-between gap-4">
        <h2 class="text-sm font-medium text-slate-500">{{ $title }}</h2>
        <x-status-badge :status="$status" />
    </div>
    <div class="mt-3 text-2xl font-semibold text-slate-950">{{ $value }}</div>
    @if ($description)
        <p class="mt-2 text-sm text-slate-500">{{ $description }}</p>
    @endif
</section>
