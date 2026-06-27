@extends('layouts.app')

@section('title', 'Device Detail')
@section('description', 'Detail satu device Windows, status telemetry, koneksi Firebird, Accurate process, dan tindakan manual.')

@section('content')
    @if (! $device)
        <x-empty-state
            title="Perangkat tidak ditemukan."
            message="Tidak ada perangkat dengan ID {{ $id }}. Perangkat dibuat berdasarkan identitas agent_id, bukan hostname."
        />
    @else
        @php
            $networkStatus = $latestNetworkCheck?->tcp_status ?? $device->firebird_connection_status ?? 'unknown';
            $accurateProcessStatus = $latestAccurateProcess?->process_status ?? $device->accurate_status ?? 'unknown';
            $rdpTargetIp = $device->ip_zerotier ?: $device->ip_local;
            $pingTargetIp = $device->ip_zerotier ?: $device->ip_local;
        @endphp
        @if (session('status'))
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                {{ session('status') }}
            </div>
        @endif
        <div class="grid gap-5 xl:grid-cols-3">
            <div class="space-y-5 xl:col-span-2">
                <x-info-panel title="Identitas" description="Identitas perangkat memakai agent_id sebagai primary identity. Hostname hanya metadata Windows.">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-950">{{ $device->display_name }}</h2>
                            <p class="mt-1 font-mono text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($device->agent_id, 10, '...') }}</p>
                        </div>
                        <x-status-badge :status="$device->display_status" />
                    </div>

                    <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-2 xl:grid-cols-4">
                        <div>
                            <dt class="text-slate-500">Hostname</dt>
                            <dd class="mt-1 font-medium text-slate-900">{{ $device->hostname }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Pengguna Windows</dt>
                            <dd class="mt-1 font-medium text-slate-900">{{ $device->windows_user ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Versi Agent</dt>
                            <dd class="mt-1 font-medium text-slate-900">{{ $device->agent_version ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Terakhir Terlihat</dt>
                            <dd class="mt-1 font-medium text-slate-900">{{ $device->last_seen_at?->diffForHumans() ?? '-' }}</dd>
                        </div>
                    </dl>
                </x-info-panel>

                <div class="grid gap-5 lg:grid-cols-2">
                    <x-info-panel title="Koneksi" description="Koneksi lokal, ZeroTier, Firebird, dan RDP terakhir yang sudah tersimpan.">
                        <dl class="space-y-4 text-sm">
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">IP Lokal</dt>
                                <dd class="font-mono text-xs text-slate-900">{{ $device->ip_local ?? '-' }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">IP ZeroTier</dt>
                                <dd class="font-mono text-xs text-slate-900">{{ $device->ip_zerotier ?? '-' }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">Firebird</dt>
                                <dd><x-status-badge :status="$networkStatus" /></dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">Host Firebird</dt>
                                <dd class="font-mono text-xs text-slate-900">{{ $latestNetworkCheck?->target_host ?? '-' }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">Port Firebird</dt>
                                <dd class="font-mono text-xs text-slate-900">{{ $latestNetworkCheck?->target_port ?? '-' }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">Latensi Firebird</dt>
                                <dd class="font-medium text-slate-900">{{ $latestNetworkCheck?->tcp_latency_ms !== null ? number_format($latestNetworkCheck->tcp_latency_ms, 0).' ms' : '-' }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">RDP</dt>
                                <dd><x-status-badge :status="$device->rdp_status" /></dd>
                            </div>
                            @if ($latestNetworkCheck)
                                <div class="rounded-md bg-slate-50 p-3 text-xs text-slate-600">
                                    Pengecekan jaringan terakhir: {{ $latestNetworkCheck->checked_at?->diffForHumans() ?? '-' }}
                                </div>
                            @endif
                        </dl>
                    </x-info-panel>

                    <x-info-panel title="Performa" description="Telemetry terakhir dari agent. Kosong berarti data belum dikirim.">
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
                                <dd class="font-medium text-slate-900">{{ $latestTelemetry?->uptime_seconds !== null ? number_format($latestTelemetry->uptime_seconds).' detik' : '-' }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">Boot Terakhir</dt>
                                <dd class="font-medium text-slate-900">{{ $latestTelemetry?->last_boot_at?->format('Y-m-d H:i') ?? '-' }}</dd>
                            </div>
                            <div class="rounded-md bg-slate-50 p-3 text-xs text-slate-600">
                                Dilaporkan: {{ $latestTelemetry?->reported_at?->diffForHumans() ?? 'Belum ada telemetry snapshot.' }}
                            </div>
                        </dl>
                    </x-info-panel>
                </div>

                <x-info-panel title="Proses Accurate" description="Status proses Accurate pada perangkat ini. Tidak dibuat critical tanpa aturan dan bukti.">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <div class="text-sm font-medium text-slate-950">{{ $latestAccurateProcess?->process_name ?? 'accurate.exe' }}</div>
                            <div class="mt-1 text-xs text-slate-500">PID: {{ $latestAccurateProcess?->process_pid ?? '-' }}</div>
                            <div class="mt-1 text-xs text-slate-500">Pemilik: {{ $latestAccurateProcess?->process_owner ?? '-' }}</div>
                            <div class="mt-1 break-all text-xs text-slate-500">Path: {{ $latestAccurateProcess?->process_path ?? '-' }}</div>
                        </div>
                        <x-status-badge :status="$accurateProcessStatus" />
                    </div>
                    <div class="mt-4 rounded-md bg-slate-50 p-3 text-xs text-slate-600">
                        Diperiksa: {{ $latestAccurateProcess?->checked_at?->diffForHumans() ?? 'Belum ada snapshot proses Accurate.' }}
                    </div>
                </x-info-panel>
            </div>

            <div class="space-y-5">
                <x-info-panel title="Tindakan" description="Remote Desktop dibuka dari perangkat admin. Restart dikirim lewat polling Windows Agent.">
                    <div class="space-y-3">
                        {{-- RDP: butuh online + IP --}}
                        @if ($device->display_status === 'online' && $rdpTargetIp)
                            <form method="POST" action="{{ route('devices.remote-actions.rdp', $device) }}">
                                @csrf
                                <x-action-button type="submit" class="w-full">Remote Desktop</x-action-button>
                            </form>
                            <div class="rounded-md bg-slate-50 p-3 text-xs text-slate-600">
                                <div class="font-medium text-slate-700">Target launcher</div>
                                <div class="mt-1 font-mono text-slate-900">mstsc /v:{{ $rdpTargetIp }}</div>
                                <div class="mt-1">Kredensial tidak disimpan atau disertakan.</div>
                            </div>
                        @else
                            <x-action-button disabled class="w-full">Remote Desktop</x-action-button>
                            <div class="rounded-md bg-amber-50 p-3 text-xs text-amber-800">
                                @if ($device->display_status !== 'online')
                                    Remote Desktop tidak tersedia karena perangkat sedang offline.
                                @else
                                    Remote Desktop tidak tersedia karena perangkat tidak memiliki IP ZeroTier atau IP lokal.
                                @endif
                            </div>
                        @endif

                        {{-- Ping: butuh online + IP --}}
                        @if ($device->display_status === 'online' && $pingTargetIp)
                            <form method="POST" action="{{ route('devices.remote-actions.ping', $device) }}">
                                @csrf
                                <x-action-button type="submit" class="w-full">Ping Test</x-action-button>
                            </form>
                        @else
                            <x-action-button disabled class="w-full">Ping Test</x-action-button>
                        @endif

                        {{-- Restart: butuh online + config enabled --}}
                        @if ($device->display_status === 'online' && config('monitoring.remote_action.restart_enabled', false))
                            <x-confirm-modal
                                title="Konfirmasi Restart Klien"
                                confirmLabel="Kirim Perintah Restart"
                                triggerLabel="Restart Klien"
                                triggerVariant="danger"
                                class="w-full"
                                formAction="{{ route('devices.remote-actions.restart', $device) }}"
                                formMethod="POST"
                                :disabled="false"
                            >
                                <p>Ini akan mengirim perintah restart manual untuk <strong>{{ $device->display_name }}</strong>. Laravel tidak akan menjalankan restart secara langsung.</p>

                                <div class="mt-4 space-y-4">
                                    <label class="block">
                                        <span class="text-sm font-medium text-slate-700">Alasan admin</span>
                                        <textarea name="reason" required minlength="5" rows="3" class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100" placeholder="Jelaskan mengapa klien ini harus direstart.">{{ old('reason') }}</textarea>
                                        @error('reason')
                                            <span class="mt-1 block text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </label>
                                    <label class="flex items-start gap-3 rounded-md border border-slate-200 p-3 text-sm text-slate-700">
                                        <input type="checkbox" name="confirm_restart" value="1" required class="mt-1 rounded border-slate-300 text-red-600 focus:ring-red-500">
                                        <span>Saya konfirmasi ini adalah tindakan manual admin dan dapat mengganggu pengguna Windows.</span>
                                    </label>
                                    @error('confirm_restart')
                                        <span class="block text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                            </x-confirm-modal>
                        @else
                            <x-action-button disabled variant="danger" class="w-full">Restart Klien</x-action-button>
                            <div class="rounded-md bg-amber-50 p-3 text-xs text-amber-800">
                                @if ($device->display_status !== 'online')
                                    Restart tidak tersedia karena perangkat sedang offline.
                                @else
                                    Restart tidak tersedia karena fitur dimatikan di Pengaturan.
                                @endif
                            </div>
                        @endif
                    </div>
                    <p class="mt-4 text-xs text-slate-500">Tidak menggunakan SSH, WinRM, pengiriman perintah RSyslog, atau remediasi otomatis.</p>
                </x-info-panel>

                <x-info-panel title="Sinyal Terkait" description="Alert, incident, dan tindakan yang sudah tersimpan untuk perangkat ini.">
                    <div class="space-y-4 text-sm">
                        <div>
                            <div class="font-medium text-slate-700">Alert</div>
                            <div class="mt-1 text-slate-500">{{ $deviceAlerts->count() }} catatan tersimpan</div>
                        </div>
                        <div>
                            <div class="font-medium text-slate-700">Incident</div>
                            <div class="mt-1 text-slate-500">{{ $deviceIncidents->count() }} catatan tersimpan</div>
                        </div>
                        <div>
                            <div class="font-medium text-slate-700">Tindakan Jarak Jauh</div>
                            <div class="mt-1 text-slate-500">{{ $deviceRemoteActions->count() }} catatan tersimpan</div>
                        </div>
                    </div>
                </x-info-panel>

                <x-info-panel title="Tindakan Terbaru" description="Audit tindakan jarak jauh terbaru untuk perangkat ini.">
                    @if ($deviceRemoteActions->isEmpty())
                        <p class="text-sm text-slate-500">Belum ada tindakan jarak jauh untuk perangkat ini.</p>
                    @else
                        <div class="space-y-3 text-sm">
                            @foreach ($deviceRemoteActions as $action)
                                <a href="{{ route('remote-actions.show', $action) }}" class="block rounded-md border border-slate-200 p-3 hover:bg-slate-50">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="font-medium text-slate-900">{{ $action->action_type }}</span>
                                        <x-status-badge :status="$action->status" />
                                    </div>
                                    <div class="mt-1 text-xs text-slate-500">
                                        {{ $action->requested_at?->format('Y-m-d H:i') ?? '-' }} oleh {{ $action->requester?->name ?? '-' }}
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