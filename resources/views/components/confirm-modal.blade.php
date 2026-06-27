@props([
    'title' => 'Confirm action',
    'confirmLabel' => 'Confirm',
    'triggerLabel' => 'Confirm',
    'triggerVariant' => 'primary',
    'formAction' => null,
    'formMethod' => 'POST',
    'disabled' => false,
])

@php
    $triggerBase = 'inline-flex items-center justify-center rounded-md px-3 py-2 text-sm font-medium transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2';
    $triggerVariants = [
        'primary' => 'bg-slate-900 text-white hover:bg-slate-800 focus-visible:outline-slate-700',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus-visible:outline-red-600',
        'secondary' => 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 focus-visible:outline-slate-500',
        'ghost' => 'text-slate-600 hover:bg-slate-100 focus-visible:outline-slate-500',
    ];
    $triggerClasses = $triggerBase.' '.($triggerVariants[$triggerVariant] ?? $triggerVariants['primary']);
    if ($disabled) {
        $triggerClasses .= ' pointer-events-none cursor-not-allowed opacity-50';
    }
@endphp

<div x-data="{ open: false }" x-id="['confirm-form']">
    <button
        type="button"
        x-on:click="open = true"
        @disabled($disabled)
        {{ $attributes->merge(['class' => $triggerClasses]) }}
    >
        {{ $triggerLabel }}
    </button>

    <div x-cloak x-show="open" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 px-4">
        <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl" x-on:click.outside="open = false">
            <h2 class="text-lg font-semibold text-slate-950">{{ $title }}</h2>

            @if ($formAction)
                <form method="{{ $formMethod }}" action="{{ $formAction }}" :id="$id('confirm-form')">
                    @csrf
                    @if (strtoupper($formMethod) !== 'GET' && strtoupper($formMethod) !== 'POST')
                        @method($formMethod)
                    @endif
                    <div class="mt-4 text-sm text-slate-600">
                        {{ $slot }}
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" x-on:click="open = false" class="rounded-md border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                            Cancel
                        </button>
                        <button type="submit" class="rounded-md bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700">
                            {{ $confirmLabel }}
                        </button>
                    </div>
                </form>
            @else
                <div class="mt-4 text-sm text-slate-600">
                    {{ $slot }}
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" x-on:click="open = false" class="rounded-md border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="button" disabled class="rounded-md bg-red-600 px-3 py-2 text-sm font-medium text-white opacity-50">
                        {{ $confirmLabel }}
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>