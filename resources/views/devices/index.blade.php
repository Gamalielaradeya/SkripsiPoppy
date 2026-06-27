@extends('layouts.app')

@section('title', 'Devices')
@section('description', 'Daftar laptop Windows pengguna Accurate 5 yang terhubung ke sistem monitoring.')

@section('content')
    <x-filter-panel description="Cari dan filter device berdasarkan status, konektivitas Firebird, dan proses Accurate.">
        <form method="GET" action="{{ route('devices.index') }}" class="grid gap-3 md:grid-cols-5">
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Search</span>
                <input name="search" value="{{ request('search') }}" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" placeholder="Label, hostname, user, IP">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Status</span>
                <select name="status" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                    <option value="">All status</option>
                    <option value="online" @selected(request('status') === 'online')>Online</option>
                    <option value="warning" @selected(request('status') === 'warning')>Warning</option>
                    <option value="error" @selected(request('status') === 'error')>Error</option>
                    <option value="offline" @selected(request('status') === 'offline')>Offline</option>
                </select>
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Firebird</span>
                <select name="firebird" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                    <option value="">All connections</option>
                    <option value="connected" @selected(request('firebird') === 'connected')>Connected</option>
                    <option value="slow" @selected(request('firebird') === 'slow')>Slow</option>
                    <option value="timeout" @selected(request('firebird') === 'timeout')>Timeout</option>
                    <option value="refused" @selected(request('firebird') === 'refused')>Refused</option>
                </select>
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Accurate</span>
                <select name="accurate" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                    <option value="">All process states</option>
                    <option value="running" @selected(request('accurate') === 'running')>Berjalan</option>
                    <option value="not_running" @selected(request('accurate') === 'not_running')>Tidak Berjalan</option>
                    <option value="unknown" @selected(request('accurate') === 'unknown')>Tidak Diketahui</option>
                </select>
            </label>
            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-slate-900 px-3 py-2 text-sm font-medium text-white transition hover:bg-slate-800">Terapkan Filter</button>
                <a href="{{ route('devices.index') }}" class="inline-flex items-center rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Reset</a>
            </div>
        </form>
    </x-filter-panel>

    @if ($devices->isEmpty())
        <x-empty-state
            title="Belum ada device terdaftar."
            message="Device akan muncul setelah Windows Agent mengirim heartbeat dan parser menyimpan data berdasarkan agent_id."
        />
    @else
        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Device Label</th>
                            <th class="px-4 py-3">Hostname</th>
                            <th class="px-4 py-3">Windows User</th>
                            <th class="px-4 py-3">ZeroTier IP</th>
                            <th class="px-4 py-3">CPU</th>
                            <th class="px-4 py-3">RAM</th>
                            <th class="px-4 py-3">Disk</th>
                            <th class="px-4 py-3">Firebird</th>
                            <th class="px-4 py-3">Accurate</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Last Seen</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach ($devices as $device)
                            @php
                                $networkStatus = $device->display_firebird_status;
                                $networkLatency = $device->latestNetworkCheck?->tcp_latency_ms;
                                $accurateProcessStatus = $device->display_accurate_status;
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <a href="{{ route('devices.show', $device) }}" class="font-medium text-slate-950 hover:text-sky-700">
                                        {{ $device->display_name }}
                                    </a>
                                    <div class="font-mono text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($device->agent_id, 10, '...') }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $device->hostname }}</td>
                                <td class="px-4 py-3">{{ $device->windows_user ?? '-' }}</td>
                                <td class="px-4 py-3 font-mono text-xs">{{ $device->ip_zerotier ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $device->latestTelemetry?->cpu_usage_percent !== null ? number_format($device->latestTelemetry->cpu_usage_percent, 1).'%' : '-' }}</td>
                                <td class="px-4 py-3">{{ $device->latestTelemetry?->ram_usage_percent !== null ? number_format($device->latestTelemetry->ram_usage_percent, 1).'%' : '-' }}</td>
                                <td class="px-4 py-3">{{ $device->latestTelemetry?->disk_usage_percent !== null ? number_format($device->latestTelemetry->disk_usage_percent, 1).'%' : '-' }}</td>
                                <td class="px-4 py-3">
                                    <x-status-badge :status="$networkStatus" />
                                </td>
                                <td class="px-4 py-3"><x-status-badge :status="$accurateProcessStatus" /></td>
                                <td class="px-4 py-3"><x-status-badge :status="$device->display_status" /></td>
                                <td class="px-4 py-3">{{ $device->last_seen_at?->diffForHumans() ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <div
                                        class="flex justify-end"
                                        x-data="{ open: false }"
                                        x-on:click.outside="open = false"
                                    >
                                        <div class="relative inline-flex rounded-md">
                                            {{-- Tombol utama: Detail — langsung buka halaman device --}}
                                            <a
                                                href="{{ route('devices.show', $device) }}"
                                                class="inline-flex items-center gap-1.5 rounded-l-md border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                                            >
                                                Detail
                                            </a>

                                            {{-- Tombol panah dropdown --}}
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-r-md border border-l-0 border-slate-200 bg-white px-2 py-2 text-sm text-slate-500 transition hover:bg-slate-50"
                                                x-on:click="open = !open"
                                            >
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </button>

                                            {{-- Menu dropdown --}}
                                            <div
                                                x-cloak
                                                x-show="open"
                                                x-transition.opacity.duration.150ms
                                                class="absolute right-0 top-full z-50 mt-1 w-40 rounded-md border border-slate-200 bg-white py-1 shadow-lg"
                                            >
                                                {{-- RDP --}}
                                                @if ($device->display_status === 'online' && ($device->ip_zerotier || $device->ip_local))
                                                    <form method="POST" action="{{ route('devices.remote-actions.rdp', $device) }}">
                                                        @csrf
                                                        <button type="submit" class="w-full px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-50">
                                                            Remote Desktop
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="block px-3 py-2 text-sm text-slate-400">Remote Desktop</span>
                                                @endif

                                                {{-- Ping --}}
                                                @if ($device->display_status === 'online' && ($device->ip_zerotier || $device->ip_local))
                                                    <form method="POST" action="{{ route('devices.remote-actions.ping', $device) }}">
                                                        @csrf
                                                        <button type="submit" class="w-full px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-50">
                                                            Ping Test
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="block px-3 py-2 text-sm text-slate-400">Ping Test</span>
                                                @endif

                                                {{-- Restart --}}
                                                @if ($device->display_status === 'online' && config('monitoring.remote_action.restart_enabled', false))
                                                    <a
                                                        href="{{ route('devices.show', $device) }}#restart"
                                                        class="block px-3 py-2 text-sm text-red-600 hover:bg-red-50"
                                                    >
                                                        Restart
                                                    </a>
                                                @else
                                                    <span class="block px-3 py-2 text-sm text-slate-400">Restart</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-4 py-3">
                {{ $devices->links() }}
            </div>
        </section>
    @endif
@endsection
