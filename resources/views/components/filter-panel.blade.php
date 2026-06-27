@props([
    'title' => 'Filter',
    'description' => 'Filter visual disiapkan untuk tahap integrasi data berikutnya.',
])

<section {{ $attributes->merge(['class' => 'rounded-lg border border-slate-200 bg-white p-4 shadow-sm']) }}>
    <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-sm font-semibold text-slate-950">{{ $title }}</h2>
            @if ($description)
                <p class="text-xs text-slate-500">{{ $description }}</p>
            @endif
        </div>
        <div class="text-xs font-medium uppercase tracking-wide text-slate-400">visual shell</div>
    </div>

    {{ $slot }}
</section>
