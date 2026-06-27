@props([
    'title',
    'description' => '',
])

<section {{ $attributes->merge(['class' => 'rounded-lg border border-slate-200 bg-white shadow-sm']) }}>
    <div class="border-b border-slate-200 px-5 py-4">
        <h2 class="text-sm font-semibold text-slate-950">{{ $title }}</h2>
        @if ($description)
            <p class="mt-1 text-xs leading-5 text-slate-500">{{ $description }}</p>
        @endif
    </div>

    <div class="p-5">
        {{ $slot }}
    </div>
</section>
