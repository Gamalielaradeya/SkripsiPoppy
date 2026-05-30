@extends('layouts.app')

@section('title', 'Device Detail')
@section('description', 'Detail satu device Windows, status telemetry, koneksi Firebird, Accurate process, dan action manual.')

@section('content')
    @if (! $device)
        <x-empty-state
            title="Device tidak ditemukan."
            message="Tidak ada device real dengan ID {{ $id }}. Device harus dibuat dari identitas agent_id, bukan hostname."
        />
    @else
        @php
            $networkStatus = $latestNetworkCheck?->tcp_status ?? $device->firebird_connection_status ?? 'unknown';
            $accurateProcessStatus = $latestAccurateProcess?->process_status ?? $device->accurate_status ?? 'unknown';
            $rdpTargetIp = $device->ip_zerotier ?: $device->ip_local;
        @endphp
        @if (session('status'))
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                {{ session('status') }}
            </div>
        @endif
        <div class="grid gap-5 xl:grid-cols-3">
            <div class="space-y-5 xl:col-span-2">
                <x-info-panel title="Identity" description="Identitas device memakai agent_id sebagai primary identity. Hostname hanya metadata Windows.">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-950">{{ $device->display_name }}</h2>
                            <p class="mt-1 font-mono text-xs text-slate-500">{{ $device->agent_id }}</p>
                        </div>
                        <x-status-badge :status="$device->status" />
                    </div>

                    <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-2 xl:grid-cols-4">
                        <div>
                            <dt class="text-slate-500">Hostname</dt>
                            <dd class="mt-1 font-medium text-slate-900">{{ $device->hostname }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Windows User</dt>
                            <dd class="mt-1 font-medium text-slate-900">{{ $device->windows_user ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Agent Version</dt>
                            <dd class="mt-1 font-medium text-slate-900">{{ $device->agent_version ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Last Seen</dt>
                            <dd class="mt-1 font-medium text-slate-900">{{ $device->last_seen_at?->diffForHumans() ?? '-' }}</dd>
                        </div>
                    </dl>
                </x-info-panel>

                <div class="grid gap-5 lg:grid-cols-2">
                    <x-info-panel title="Connection" description="Koneksi lokal, ZeroTier, Firebird, dan RDP terakhir yang sudah tersimpan.">
                        <dl class="space-y-4 text-sm">
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">Local IP</dt>
                                <dd class="font-mono text-xs text-slate-900">{{ $device->ip_local ?? '-' }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">ZeroTier IP</dt>
                                <dd class="font-mono text-xs text-slate-900">{{ $device->ip_zerotier ?? '-' }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">Firebird</dt>
                                <dd><x-status-badge :status="$networkStatus" /></dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">Firebird Host</dt>
                                <dd class="font-mono text-xs text-slate-900">{{ $latestNetworkCheck?->target_host ?? '-' }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">Firebird Port</dt>
                                <dd class="font-mono text-xs text-slate-900">{{ $latestNetworkCheck?->target_port ?? '-' }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">Firebird Latency</dt>
                                <dd class="font-medium text-slate-900">{{ $latestNetworkCheck?->tcp_latency_ms !== null ? number_format($latestNetworkCheck->tcp_latency_ms, 0).' ms' : '-' }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">RDP</dt>
                                <dd><x-status-badge :status="$device->rdp_status" /></dd>
                            </div>
                            @if ($latestNetworkCheck)
                                <div class="rounded-md bg-slate-50 p-3 text-xs text-slate-600">
                                    Last network check: {{ $latestNetworkCheck->checked_at?->diffForHumans() ?? '-' }}
                                </div>
                            @endif
                        </dl>
                    </x-info-panel>

                    <x-info-panel title="Performance" description="Telemetry terakhir dari agent. Kosong berarti data belum dikirim.">
                        <dl class="space-y-4 text-sm">
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">CPU</dt>
                                <dd class="font-medium text-slate-900">{{ $latestTelemetry?->cpu_usage_percent !== null ? number_format($latestTelemetry->cpu_usage_percent, 1).'%' : '-' }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">RAM</dt>
                                <dd class="font-medium text-slate-900">{{ $latestTelemetry?->ram_usage_percent !== null ? number_format($latestTelemetry->ram_usage_percent, 1).'%' : '-' }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">Disk</dt>
                                <dd class="font-medium text-slate-900">{{ $latestTelemetry?->disk_usage_percent !== null ? number_format($latestTelemetry->disk_usage_percent, 1).'%' : '-' }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">Uptime</dt>
                                <dd class="font-medium text-slate-900">{{ $latestTelemetry?->uptime_seconds !== null ? number_format($latestTelemetry->uptime_seconds).' sec' : '-' }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">Last Boot</dt>
                                <dd class="font-medium text-slate-900">{{ $latestTelemetry?->last_boot_at?->format('Y-m-d H:i') ?? '-' }}</dd>
                            </div>
                            <div class="rounded-md bg-slate-50 p-3 text-xs text-slate-600">
                                Reported at: {{ $latestTelemetry?->reported_at?->diffForHumans() ?? 'Belum ada telemetry snapshot.' }}
                            </div>
                        </dl>
                    </x-info-panel>
                </div>

                <x-info-panel title="Accurate Process" description="Status proses Accurate pada device ini. Tidak dibuat critical tanpa rule dan evidence.">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <div class="text-sm font-medium text-slate-950">{{ $latestAccurateProcess?->process_name ?? 'accurate.exe' }}</div>
                            <div class="mt-1 text-xs text-slate-500">PID: {{ $latestAccurateProcess?->process_pid ?? '-' }}</div>
                            <div class="mt-1 text-xs text-slate-500">Owner: {{ $latestAccurateProcess?->process_owner ?? '-' }}</div>
                            <div class="mt-1 break-all text-xs text-slate-500">Path: {{ $latestAccurateProcess?->process_path ?? '-' }}</div>
                        </div>
                        <x-status-badge :status="$accurateProcessStatus" />
                    </div>
                    <div class="mt-4 rounded-md bg-slate-50 p-3 text-xs text-slate-600">
                        Checked at: {{ $latestAccurateProcess?->checked_at?->diffForHumans() ?? 'Belum ada snapshot proses Accurate.' }}
                    </div>
                </x-info-panel>
            </div>

            <div class="space-y-5">
                <x-info-panel title="Actions" description="Remote Desktop dibuka dari perangkat admin. Restart dikirim lewat polling Windows Agent.">
                    <div class="space-y-3">
                        <form method="POST" action="{{ route('devices.remote-actions.rdp', $device) }}">
                            @csrf
                            <x-action-button type="submit" :disabled="! $rdpTargetIp" class="w-full">Remote Desktop</x-action-button>
                        </form>

                        @if ($rdpTargetIp)
                            <div class="rounded-md bg-slate-50 p-3 text-xs text-slate-600">
                                <div class="font-medium text-slate-700">Launcher target</div>
                                <div class="mt-1 font-mono text-slate-900">mstsc /v:{{ $rdpTargetIp }}</div>
                                <div class="mt-1">Credentials are not stored or included.</div>
                            </div>
                        @else
                            <div class="rounded-md bg-amber-50 p-3 text-xs text-amber-800">
                                Remote Desktop unavailable because this device has no ZeroTier or local IP.
                            </div>
                        @endif

                        <div x-data="{ open: false }">
                            <x-action-button variant="danger" class="w-full" x-on:click="open = true">Restart Client</x-action-button>

                            <div x-cloak x-show="open" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 px-4">
                                <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl" x-on:click.outside="open = false">
                                    <h2 class="text-lg font-semibold text-slate-950">Confirm Restart Client</h2>
                                    <p class="mt-2 text-sm text-slate-600">
                                        This queues a manual restart command for {{ $device->display_name }}. Laravel will not execute the restart directly.
                                    </p>

                                    <form method="POST" action="{{ route('devices.remote-actions.restart', $device) }}" class="mt-5 space-y-4">
                                        @csrf
                                        <label class="block">
                                            <span class="text-sm font-medium text-slate-700">Admin reason</span>
                                            <textarea name="reason" required minlength="5" rows="4" class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100" placeholder="Explain why this client must be restarted.">{{ old('reason') }}</textarea>
                                            @error('reason')
                                                <span class="mt-1 block text-xs text-red-600">{{ $message }}</span>
                                            @enderror
                                        </label>

                                        <label class="flex items-start gap-3 rounded-md border border-slate-200 p-3 text-sm text-slate-700">
                                            <input type="checkbox" name="confirm_restart" value="1" required class="mt-1 rounded border-slate-300 text-red-600 focus:ring-red-500">
                                            <span>I confirm this is a manual admin action and may interrupt the Windows user.</span>
                                        </label>
                                        @error('confirm_restart')
                                            <span class="block text-xs text-red-600">{{ $message }}</span>
                                        @enderror

                                        <div class="flex justify-end gap-3">
                                            <button type="button" x-on:click="open = false" class="rounded-md border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700">Cancel</button>
                                            <button type="submit" class="rounded-md bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700">Queue Restart</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="mt-4 text-xs text-slate-500">No SSH, WinRM, RSyslog command delivery, or automatic remediation is used.</p>
                </x-info-panel>

                <x-info-panel title="Related Signals" description="Alert, incident, dan action yang sudah tersimpan untuk device ini.">
                    <div class="space-y-4 text-sm">
                        <div>
                            <div class="font-medium text-slate-700">Alerts</div>
                            <div class="mt-1 text-slate-500">{{ $deviceAlerts->count() }} stored record(s)</div>
                        </div>
                        <div>
                            <div class="font-medium text-slate-700">Incidents</div>
                            <div class="mt-1 text-slate-500">{{ $deviceIncidents->count() }} stored record(s)</div>
                        </div>
                        <div>
                            <div class="font-medium text-slate-700">Remote Actions</div>
                            <div class="mt-1 text-slate-500">{{ $deviceRemoteActions->count() }} stored record(s)</div>
                        </div>
                    </div>
                </x-info-panel>

                <x-info-panel title="Recent Remote Actions" description="Audit trail terbaru untuk device ini.">
                    @if ($deviceRemoteActions->isEmpty())
                        <p class="text-sm text-slate-500">Belum ada remote action untuk device ini.</p>
                    @else
                        <div class="space-y-3 text-sm">
                            @foreach ($deviceRemoteActions as $action)
                                <a href="{{ route('remote-actions.show', $action) }}" class="block rounded-md border border-slate-200 p-3 hover:bg-slate-50">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="font-medium text-slate-900">{{ $action->action_type }}</span>
                                        <x-status-badge :status="$action->status" />
                                    </div>
                                    <div class="mt-1 text-xs text-slate-500">
                                        {{ $action->requested_at?->format('Y-m-d H:i') ?? '-' }} by {{ $action->requester?->name ?? '-' }}
                                    </div>
                                    <div class="mt-2 text-xs text-slate-600">{{ \Illuminate\Support\Str::limit($action->reason ?? $action->result_message ?? '-', 90) }}</div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </x-info-panel>
            </div>
        </div>
    @endif
@endsection
