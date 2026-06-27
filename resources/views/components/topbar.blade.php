@php
    $openAlertCount = \App\Models\Alert::query()->where('status', 'open')->count();
@endphp

<header class="border-b border-slate-200 bg-white">
    <div class="flex min-h-16 items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
        <div class="flex min-w-0 items-center gap-3">
            <button
                type="button"
                class="rounded-md border border-slate-200 px-2.5 py-1.5 text-sm text-slate-600 transition hover:bg-slate-50 lg:hidden"
                x-on:click="sidebarOpen = ! sidebarOpen"
                aria-label="Toggle sidebar"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
            <div class="min-w-0">
                <div class="truncate text-sm font-semibold text-slate-900">{{ config('app.name') }}</div>
                <div class="hidden text-xs text-slate-500 sm:block">Real-device Accurate monitoring workspace</div>
            </div>
        </div>

        <div class="flex shrink-0 items-center gap-3 text-sm text-slate-500">
            <span class="hidden lg:inline">Last refresh: {{ now()->format('H:i:s') }}</span>
            <a href="{{ route('alerts.index') }}" class="rounded-full {{ $openAlertCount > 0 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }} px-3 py-1 text-xs font-semibold">
                Open Alerts: {{ $openAlertCount }}
            </a>
            <span class="font-medium text-slate-700">{{ auth()->user()->name ?? 'Administrator' }}</span>
        </div>
    </div>
</header>
