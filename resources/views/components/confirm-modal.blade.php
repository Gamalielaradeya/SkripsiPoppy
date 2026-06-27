@props([
    'title' => 'Confirm action',
    'confirmLabel' => 'Confirm',
    'submitLabel' => null,
    'disabled' => false,
])

<div x-data="{ open: false }">
    <button type="button" x-on:click="open = true" @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-md bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50']) }}>
        {{ $confirmLabel }}
    </button>

    <div x-cloak x-show="open" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 px-4">
        <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl" x-on:click.outside="open = false">
            <h2 class="text-lg font-semibold text-slate-950">{{ $title }}</h2>
            <div class="mt-4 text-sm text-slate-600">
                {{ $slot }}
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="open = false" class="rounded-md border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700">
                    Cancel
                </button>
                @if ($submitLabel)
                    <button type="button" disabled class="rounded-md bg-red-600 px-3 py-2 text-sm font-medium text-white opacity-50">
                        {{ $submitLabel }}
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
