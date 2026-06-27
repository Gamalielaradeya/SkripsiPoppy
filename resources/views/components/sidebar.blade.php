@php
    $items = [
        ['label' => 'Dashboard', 'route' => 'dashboard.index', 'active' => 'dashboard.*', 'mark' => 'DS'],
        ['label' => 'Devices', 'route' => 'devices.index', 'active' => 'devices.*', 'mark' => 'DV'],
        ['label' => 'Accurate Audit', 'route' => 'accurate-audit.index', 'active' => 'accurate-audit.*', 'mark' => 'AA'],
        ['label' => 'Incidents', 'route' => 'incidents.index', 'active' => 'incidents.*', 'mark' => 'IN'],
        ['label' => 'Alerts', 'route' => 'alerts.index', 'active' => 'alerts.*', 'mark' => 'AL'],
        ['label' => 'Tindakan Admin', 'route' => 'remote-actions.index', 'active' => 'remote-actions.*', 'mark' => 'TA'],
        ['label' => 'Advanced Logs', 'route' => 'advanced-logs.index', 'active' => 'advanced-logs.*', 'mark' => 'LG'],
        ['label' => 'Settings', 'route' => 'settings.index', 'active' => 'settings.*', 'mark' => 'ST'],
    ];
@endphp

<aside
    class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full border-r border-slate-800 bg-slate-950 text-slate-300 transition lg:static lg:w-64 lg:translate-x-0"
    :class="{ 'translate-x-0': sidebarOpen }"
>
    <div class="flex h-full flex-col">
        <div class="border-b border-slate-800 px-5 py-5">
            <div class="text-sm font-semibold uppercase tracking-wide text-white">Centralized Monitor</div>
            <div class="mt-1 text-xs text-slate-400">Accurate real-device cockpit</div>
        </div>

        <nav class="flex-1 space-y-1 px-3 py-4" aria-label="Primary navigation">
            @foreach ($items as $item)
                @php($active = request()->routeIs($item['active']))
                <a
                    href="{{ route($item['route']) }}"
                    class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition {{ $active ? 'bg-slate-800 text-white shadow-inner shadow-black/10' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}"
                    @if($active) aria-current="page" @endif
                >
                    <span class="flex h-7 w-7 items-center justify-center rounded border text-[10px] font-semibold {{ $active ? 'border-sky-400 bg-sky-400/10 text-sky-200' : 'border-slate-800 bg-slate-900 text-slate-500 group-hover:text-slate-300' }}">{{ $item['mark'] }}</span>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="border-t border-slate-800 p-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full rounded-md px-3 py-2 text-left text-sm font-medium text-slate-300 transition hover:bg-slate-900 hover:text-white">
                    Logout
                </button>
            </form>
        </div>
    </div>
</aside>
