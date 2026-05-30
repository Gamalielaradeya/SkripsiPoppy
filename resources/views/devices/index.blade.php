@extends('layouts.app')

@section('title', 'Devices')
@section('description', 'Daftar laptop Windows pengguna Accurate 5 yang terhubung ke sistem monitoring.')

@section('content')
    <x-filter-panel description="Filter ini disiapkan untuk pencarian device setelah data telemetry stabil. Saat ini tabel memakai record database yang ada.">
        <div class="grid gap-3 md:grid-cols-5">
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Search</span>
                <input disabled class="mt-1 w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-400" placeholder="Label, hostname, user, IP">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Status</span>
                <select disabled class="mt-1 w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-400">
                    <option>All status</option>
                </select>
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Firebird</span>
                <select disabled class="mt-1 w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-400">
                    <option>All connections</option>
                </select>
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Accurate</span>
                <select disabled class="mt-1 w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-400">
                    <option>All process states</option>
                </select>
            </label>
            <div class="flex items-end">
                <x-action-button disabled class="w-full">Apply Filter</x-action-button>
            </div>
        </div>
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
                                $networkStatus = $device->latestNetworkCheck?->tcp_status ?? $device->firebird_connection_status ?? 'unknown';
                                $networkLatency = $device->latestNetworkCheck?->tcp_latency_ms;
                                $accurateProcessStatus = $device->latestAccurateProcessSnapshot?->process_status ?? $device->accurate_status ?? 'unknown';
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <a href="{{ route('devices.show', $device) }}" class="font-medium text-slate-950 hover:text-sky-700">
                                        {{ $device->display_name }}
                                    </a>
                                    <div class="font-mono text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($device->agent_id, 18) }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $device->hostname }}</td>
                                <td class="px-4 py-3">{{ $device->windows_user ?? '-' }}</td>
                                <td class="px-4 py-3 font-mono text-xs">{{ $device->ip_zerotier ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $device->latestTelemetry?->cpu_usage_percent !== null ? number_format($device->latestTelemetry->cpu_usage_percent, 1).'%' : '-' }}</td>
                                <td class="px-4 py-3">{{ $device->latestTelemetry?->ram_usage_percent !== null ? number_format($device->latestTelemetry->ram_usage_percent, 1).'%' : '-' }}</td>
                                <td class="px-4 py-3">{{ $device->latestTelemetry?->disk_usage_percent !== null ? number_format($device->latestTelemetry->disk_usage_percent, 1).'%' : '-' }}</td>
                                <td class="px-4 py-3">
                                    <x-status-badge :status="$networkStatus" />
                                    <div class="mt-1 text-xs text-slate-500">{{ $networkLatency !== null ? number_format($networkLatency, 0).' ms' : '-' }}</div>
                                </td>
                                <td class="px-4 py-3"><x-status-badge :status="$accurateProcessStatus" /></td>
                                <td class="px-4 py-3"><x-status-badge :status="$device->display_status" /></td>
                                <td class="px-4 py-3">{{ $device->last_seen_at?->diffForHumans() ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <x-action-button :href="route('devices.show', $device)" variant="secondary">Detail</x-action-button>
                                        <x-action-button disabled variant="ghost">RDP</x-action-button>
                                        <x-action-button disabled variant="danger">Restart</x-action-button>
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
