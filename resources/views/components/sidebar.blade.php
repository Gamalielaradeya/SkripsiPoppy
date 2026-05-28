@php
    $items = [
        ['label' => 'Dashboard', 'route' => 'dashboard.index', 'active' => 'dashboard.*'],
        ['label' => 'Devices', 'route' => 'devices.index', 'active' => 'devices.*'],
        ['label' => 'Accurate Audit', 'route' => 'accurate-audit.index', 'active' => 'accurate-audit.*'],
        ['label' => 'Incidents', 'route' => 'incidents.index', 'active' => 'incidents.*'],
        ['label' => 'Alerts', 'route' => 'alerts.index', 'active' => 'alerts.*'],
        ['label' => 'Remote Actions', 'route' => 'remote-actions.index', 'active' => 'remote-actions.*'],
        ['label' => 'Advanced Logs', 'route' => 'advanced-logs.index', 'active' => 'advanced-logs.*'],
        ['label' => 'Settings', 'route' => 'settings.index', 'active' => 'settings.*'],
    ];
@endphp

<aside
    class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full bg-slate-950 text-slate-300 transition lg:static lg:translate-x-0"
    :class="{ 'translate-x-0': sidebarOpen }"
>
    <div class="flex h-full flex-col">
        <div class="border-b border-slate-800 px-5 py-5">
            <div class="text-sm font-semibold uppercase tracking-wide text-white">Centralized Monitor</div>
            <div class="mt-1 text-xs text-slate-400">IT operations cockpit</div>
        </div>

        <nav class="flex-1 space-y-1 px-3 py-4">
            @foreach ($items as $item)
                <a
                    href="{{ route($item['route']) }}"
                    class="block rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs($item['active']) ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="border-t border-slate-800 p-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full rounded-md px-3 py-2 text-left text-sm font-medium text-slate-300 hover:bg-slate-900 hover:text-white">
                    Logout
                </button>
            </form>
        </div>
    </div>
</aside>
