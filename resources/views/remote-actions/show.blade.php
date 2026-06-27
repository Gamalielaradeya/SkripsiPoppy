@extends('layouts.app')

@section('title', 'Detail Tindakan Jarak Jauh')
@section('description', 'Detail tindakan remote manual dan hasil dari Windows Agent.')

@section('content')
    @if (! $remoteAction)
        <x-empty-state
            title="Tindakan jarak jauh tidak ditemukan."
            message="Tidak ada tindakan jarak jauh dengan ID {{ $id }}."
        />
    @else
        @php
            $payload = $remoteAction->payload ?? [];
            $mstscCommand = $payload['mstsc_command'] ?? (isset($payload['target_ip']) ? 'mstsc /v:'.$payload['target_ip'] : null);
        @endphp

        <div class="grid gap-5 xl:grid-cols-3">
            <div class="space-y-5 xl:col-span-2">
                <x-info-panel title="Ringkasan Tindakan" description="Audit utama untuk tindakan jarak jauh manual.">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-950">{{ $remoteAction->action_type }}</h2>
                            <p class="mt-1 text-sm text-slate-500">{{ $remoteAction->device?->display_name ?? 'Perangkat tidak dikenal' }}</p>
                        </div>
                        <x-status-badge :status="$remoteAction->status" />
                    </div>

                    <dl class="mt-6 grid gap-4 text-sm md:grid-cols-2">
                        <div>
                            <dt class="text-slate-500">Diminta Oleh</dt>
                            <dd class="mt-1 font-medium text-slate-900">{{ $remoteAction->requester?->name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Perangkat Target</dt>
                            <dd class="mt-1 font-medium text-slate-900">{{ $remoteAction->device?->display_name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Waktu Permintaan</dt>
                            <dd class="mt-1 font-medium text-slate-900">{{ $remoteAction->requested_at?->format('Y-m-d H:i:s') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Kedaluwarsa</dt>
                            <dd class="mt-1 font-medium text-slate-900">{{ $remoteAction->expires_at?->format('Y-m-d H:i:s') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Diambil Pada</dt>
                            <dd class="mt-1 font-medium text-slate-900">{{ $remoteAction->picked_up_at?->format('Y-m-d H:i:s') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Selesai Pada</dt>
                            <dd class="mt-1 font-medium text-slate-900">{{ $remoteAction->completed_at?->format('Y-m-d H:i:s') ?? '-' }}</dd>
                        </div>
                    </dl>
                </x-info-panel>

                @if ($remoteAction->action_type === \App\Models\RemoteAction::ACTION_OPEN_RDP && $mstscCommand)
                    <x-info-panel title="Launcher Remote Desktop" description="Launcher aman untuk dibuka dari perangkat admin. Tidak ada kredensial tersimpan.">
                        <div class="rounded-md bg-slate-950 p-3 font-mono text-sm text-white">
                            {{ $mstscCommand }}
                        </div>
                        <div class="mt-4 flex flex-wrap gap-3">
                            <x-action-button :href="route('remote-actions.rdp-file', $remoteAction)">Unduh File .rdp</x-action-button>
                            <x-action-button variant="secondary" :href="route('devices.show', $remoteAction->device_id)">Kembali ke Perangkat</x-action-button>
                        </div>
                    </x-info-panel>
                @endif

                <x-info-panel title="Hasil" description="Status akhir yang dikirim Windows Agent atau disiapkan dashboard.">
                    <dl class="space-y-4 text-sm">
                        <div>
                            <dt class="text-slate-500">Pesan Hasil</dt>
                            <dd class="mt-1 text-slate-900">{{ $remoteAction->result_message ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Pesan Kesalahan</dt>
                            <dd class="mt-1 text-slate-900">{{ $remoteAction->error_message ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Dijalankan Pada</dt>
                            <dd class="mt-1 text-slate-900">{{ $remoteAction->executed_at?->format('Y-m-d H:i:s') ?? '-' }}</dd>
                        </div>
                    </dl>
                </x-info-panel>
            </div>

            <div class="space-y-5">
                <x-info-panel title="Konfirmasi" description="Restart wajib dikonfirmasi admin dan disertai alasan.">
                    <dl class="space-y-4 text-sm">
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-slate-500">Diperlukan</dt>
                            <dd class="font-medium text-slate-900">{{ $remoteAction->requires_confirmation ? 'Ya' : 'Tidak' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-slate-500">Dikonfirmasi Oleh</dt>
                            <dd class="font-medium text-slate-900">{{ $remoteAction->confirmer?->name ?? '-' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-slate-500">Dikonfirmasi Pada</dt>
                            <dd class="font-medium text-slate-900">{{ $remoteAction->confirmed_at?->format('Y-m-d H:i:s') ?? '-' }}</dd>
                        </div>
                    </dl>
                </x-info-panel>

                <x-info-panel title="Alasan" description="Alasan admin yang tersimpan untuk keperluan audit.">
                    <p class="whitespace-pre-line text-sm text-slate-700">{{ $remoteAction->reason ?? '-' }}</p>
                </x-info-panel>

                <x-info-panel title="Payload" description="Payload aman tanpa kredensial Windows atau RDP.">
                    <pre class="max-h-80 overflow-auto rounded-md bg-slate-950 p-3 text-xs text-white">{{ json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                </x-info-panel>
            </div>
        </div>
    @endif
@endsection