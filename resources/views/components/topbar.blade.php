<header class="border-b border-slate-200 bg-white">
    <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <div class="flex min-w-0 items-center gap-3">
            <button
                type="button"
                class="rounded-md border border-slate-200 px-2 py-1 text-sm text-slate-600 lg:hidden"
                x-on:click="sidebarOpen = ! sidebarOpen"
            >
                Menu
            </button>
            <div class="truncate text-sm font-semibold text-slate-900">{{ config('app.name') }}</div>
        </div>

        <div class="flex items-center gap-4 text-sm text-slate-500">
            <span class="hidden sm:inline">Last refresh: not available</span>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">Open Alerts: -</span>
            <span class="font-medium text-slate-700">{{ auth()->user()->name ?? 'Administrator' }}</span>
        </div>
    </div>
</header>
