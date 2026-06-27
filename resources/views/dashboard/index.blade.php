@extends('layouts.app')

@section('title', 'Dashboard')
@section('description', 'Ringkasan kondisi device Accurate, koneksi Firebird, audit, alert, dan incident.')

@section('content')
    @php
        $deviceStatus = $totalDevices === 0 ? 'unknown' : ($onlineDevices === $totalDevices ? 'normal' : 'warning');
        $firebirdStatus = $totalDevices === 0 ? 'unknown' : ($firebirdConnectedDevices === $totalDevices ? 'normal' : 'warning');
        $accurateStatus = $totalDevices === 0 ? 'unknown' : ($accurateRunningDevices === $totalDevices ? 'normal' : 'warning');
        $alertStatus = $openAlerts > 0 ? 'warning' : 'normal';
    @endphp

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
        <x-status-card title="Device Online" :value="$onlineDevices . ' / ' . $totalDevices" :status="$deviceStatus" href="{{ route('devices.index') }}" description="Status operasional dihitung dari waktu heartbeat terakhir." />
        <x-status-card title="Firebird OK" :value="$firebirdConnectedDevices . ' / ' . $totalDevices" :status="$firebirdStatus" href="{{ route('devices.index') }}" description="Status koneksi client ke Firebird dari data real." />
        <x-status-card title="Accurate Active" :value="$accurateRunningDevices . ' / ' . $totalDevices" :status="$accurateStatus" href="{{ route('devices.index') }}" description="Status proses Accurate pada device terdaftar." />
        <x-status-card title="Open Alerts" :value="$openAlerts" :status="$alertStatus" href="{{ route('alerts.index') }}" description="Alert terbuka yang sudah tersimpan." />
        <x-status-card title="Audit Today" :value="$auditEventsToday" status="normal" href="{{ route('accurate-audit.index') }}" description="Event audit Accurate yang tersimpan hari ini." />
        <x-status-card title="Open Incidents" :value="$openIncidents" :status="$openIncidents > 0 ? 'warning' : 'normal'" href="{{ route('incidents.index') }}" description="Incident terbuka yang perlu ditangani admin." />
    </div>

    @if ($totalDevices === 0)
        <x-empty-state
            title="Belum ada data monitoring real-device."
            message="Dashboard ini sengaja tidak memakai data palsu. Data akan tampil setelah Windows Agent dan parser menyimpan status berdasarkan agent_id."
        />
    @endif

    <div class="grid gap-5 xl:grid-cols-3">
        <x-info-panel title="Device Health" description="Ringkasan device Windows Accurate berdasarkan data yang sudah masuk." class="xl:col-span-2">
            @if ($latestDevices->isEmpty())
                <x-empty-state
                    class="border-slate-200 p-6"
                    title="Belum ada device untuk ditampilkan."
                    message="Jalankan Windows Agent pada laptop client agar device muncul dengan agent_id, hostname, user Windows, dan status heartbeat."
                />
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-3 py-2">Device</th>
                                <th class="px-3 py-2">Windows User</th>
                                <th class="px-3 py-2">CPU</th>
                                <th class="px-3 py-2">RAM</th>
                                <th class="px-3 py-2">Disk</th>
                                <th class="px-3 py-2">ZeroTier</th>
                                <th class="px-3 py-2">Firebird</th>
                                <th class="px-3 py-2">Accurate</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach ($latestDevices as $device)
                                @php
                                    $networkStatus = $device->latestNetworkCheck?->tcp_status ?? $device->firebird_connection_status ?? 'unknown';
                                    $networkLatency = $device->latestNetworkCheck?->tcp_latency_ms;
                                    $accurateProcessStatus = $device->latestAccurateProcessSnapshot?->process_status ?? $device->accurate_status ?? 'unknown';
                                @endphp
                                <tr>
                                    <td class="px-3 py-3">
                                        <div class="font-medium text-slate-950">{{ $device->display_name }}</div>
                                        <div class="text-xs text-slate-500">{{ $device->hostname }}</div>
                                    </td>
                                    <td class="px-3 py-3">{{ $device->windows_user ?? '-' }}</td>
                                    <td class="px-3 py-3">{{ $device->latestTelemetry?->cpu_usage_percent !== null ? number_format($device->latestTelemetry->cpu_usage_percent, 1).'%' : '-' }}</td>
                                    <td class="px-3 py-3">{{ $device->latestTelemetry?->ram_usage_percent !== null ? number_format($device->latestTelemetry->ram_usage_percent, 1).'%' : '-' }}</td>
                                    <td class="px-3 py-3">{{ $device->latestTelemetry?->disk_usage_percent !== null ? number_format($device->latestTelemetry->disk_usage_percent, 1).'%' : '-' }}</td>
                                    <td class="px-3 py-3 font-mono text-xs">{{ $device->ip_zerotier ?? '-' }}</td>
                                    <td class="px-3 py-3">
                                        <x-status-badge :status="$networkStatus" />
                                        <div class="mt-1 text-xs text-slate-500">{{ $networkLatency !== null ? number_format($networkLatency, 0).' ms' : '-' }}</div>
                                    </td>
                                    <td class="px-3 py-3"><x-status-badge :status="$accurateProcessStatus" /></td>
                                    <td class="px-3 py-3"><x-status-badge :status="$device->display_status" /></td>
                                    <td class="px-3 py-3">
                                        <x-action-button :href="route('devices.show', $device)" variant="secondary">Detail</x-action-button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-info-panel>

        <x-info-panel title="Recent Alerts" description="Alert terbuka harus punya target, evidence, impact, dan tindakan saran.">
            @if ($latestAlerts->isEmpty())
                <x-empty-state
                    class="border-slate-200 p-6"
                    title="Tidak ada alert terbuka."
                    message="Alert akan muncul setelah detection logic resmi membuat alert berbasis evidence."
                />
            @else
                <div class="space-y-3">
                    @foreach ($latestAlerts as $alert)
                        <a href="{{ route('alerts.show', $alert) }}" class="block rounded-md border border-slate-200 p-3 hover:bg-slate-50">
                            <div class="flex items-center justify-between gap-3">
                                <div class="font-medium text-slate-950">{{ $alert->title }}</div>
                                <x-severity-badge :severity="$alert->severity" />
                            </div>
                            <div class="mt-1 text-xs text-slate-500">Target: {{ $alert->target_name ?? '-' }}</div>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-info-panel>
    </div>

    <div class="grid gap-5 lg:grid-cols-2">
        <x-info-panel title="Recent Accurate Audit" description="Aktivitas terbaru dari Firebird AUDIT + USERS jika sudah tersinkronisasi.">
            @if ($latestAuditEvents->isEmpty())
                <x-empty-state
                    class="border-slate-200 p-6"
                    title="Belum ada audit Accurate."
                    message="Panel ini tetap kosong sampai Firebird Audit Reader menyimpan event real dari AUDIT + USERS."
                />
            @else
                <div class="space-y-3">
                    @foreach ($latestAuditEvents as $event)
                        <a href="{{ route('accurate-audit.show', $event) }}" class="block rounded-md border border-slate-200 p-3 hover:bg-slate-50">
                            <div class="flex items-center justify-between gap-3">
                                <div class="font-medium text-slate-950">{{ $event->accurate_username ?? 'Unknown Accurate User' }}</div>
                                <div class="text-xs text-slate-500">{{ $event->activity_time?->format('Y-m-d H:i') ?? '-' }}</div>
                            </div>
                            <div class="mt-1 text-sm text-slate-600">{{ $event->transaction_description ?? $event->source ?? '-' }}</div>
                            <div class="mt-1 text-xs text-slate-500">Reference: {{ $event->invoice_no ?? '-' }}</div>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-info-panel>

        <x-info-panel title="Recent Incidents" description="Masalah operasional hasil korelasi akan tampil di sini setelah milestone incident.">
            @if ($latestIncidents->isEmpty())
                <x-empty-state
                    class="border-slate-200 p-6"
                    title="Belum ada incident terbuka."
                    message="Incident tidak dibuat dari asumsi. Korelasi akan muncul jika alert/event punya hubungan operasional yang jelas."
                />
            @else
                <div class="space-y-3">
                    @foreach ($latestIncidents as $incident)
                        <a href="{{ route('incidents.show', $incident) }}" class="block rounded-md border border-slate-200 p-3 hover:bg-slate-50">
                            <div class="flex items-center justify-between gap-3">
                                <div class="font-medium text-slate-950">{{ $incident->title }}</div>
                                <x-severity-badge :severity="$incident->severity" />
                            </div>
                            <div class="mt-1 text-sm text-slate-600">{{ $incident->summary ?? '-' }}</div>
                            <div class="mt-1 text-xs text-slate-500">Target: {{ $incident->target_name ?? '-' }}</div>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-info-panel>
    </div>
@endsection
