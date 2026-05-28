@props([
    'title' => 'Page',
    'description' => '',
])

<header class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h1 class="text-2xl font-semibold text-slate-950">{{ $title ?: 'Page' }}</h1>
        @if ($description)
            <p class="mt-1 max-w-3xl text-sm text-slate-500">{{ $description }}</p>
        @endif
    </div>
    @if (trim($slot) !== '')
        <div>{{ $slot }}</div>
    @endif
</header>
