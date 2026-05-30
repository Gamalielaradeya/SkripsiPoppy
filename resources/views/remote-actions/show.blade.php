@extends('layouts.app')

@section('title', 'Remote Action Detail')
@section('description', 'Detail tindakan remote manual dan hasil dari Windows Agent.')

@section('content')
    @if (! $remoteAction)
        <x-empty-state
            title="Remote action tidak ditemukan."
            message="Tidak ada remote action real dengan ID {{ $id }}."
        />
    @else
        @php
            $payload = $remoteAction->payload ?? [];
            $mstscCommand = $payload['mstsc_command'] ?? (isset($payload['target_ip']) ? 'mstsc /v:'.$payload['target_ip'] : null);
        @endphp

        <div class="grid gap-5 xl:grid-cols-3">
            <div class="space-y-5 xl:col-span-2">
                <x-info-panel title="Action Summary" description="Audit utama untuk tindakan remote manual.">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-950">{{ $remoteAction->action_type }}</h2>
                            <p class="mt-1 text-sm text-slate-500">{{ $remoteAction->device?->display_name ?? 'Unknown device' }}</p>
                        </div>
                        <x-status-badge :status="$remoteAction->status" />
                    </div>

                    <dl class="mt-6 grid gap-4 text-sm md:grid-cols-2">
                        <div>
                            <dt class="text-slate-500">Requester</dt>
                            <dd class="mt-1 font-medium text-slate-900">{{ $remoteAction->requester?->name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Target Device</dt>
                            <dd class="mt-1 font-medium text-slate-900">{{ $remoteAction->device?->display_name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Requested At</dt>
                            <dd class="mt-1 font-medium text-slate-900">{{ $remoteAction->requested_at?->format('Y-m-d H:i:s') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Expires At</dt>
                            <dd class="mt-1 font-medium text-slate-900">{{ $remoteAction->expires_at?->format('Y-m-d H:i:s') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Picked Up At</dt>
                            <dd class="mt-1 font-medium text-slate-900">{{ $remoteAction->picked_up_at?->format('Y-m-d H:i:s') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Completed At</dt>
                            <dd class="mt-1 font-medium text-slate-900">{{ $remoteAction->completed_at?->format('Y-m-d H:i:s') ?? '-' }}</dd>
                        </div>
                    </dl>
                </x-info-panel>

                @if ($remoteAction->action_type === \App\Models\RemoteAction::ACTION_OPEN_RDP && $mstscCommand)
                    <x-info-panel title="Remote Desktop Launcher" description="Launcher aman untuk dibuka dari perangkat admin. Tidak ada credential tersimpan.">
                        <div class="rounded-md bg-slate-950 p-3 font-mono text-sm text-white">
                            {{ $mstscCommand }}
                        </div>
                        <div class="mt-4 flex flex-wrap gap-3">
                            <x-action-button :href="route('remote-actions.rdp-file', $remoteAction)">Download .rdp File</x-action-button>
                            <x-action-button variant="secondary" :href="route('devices.show', $remoteAction->device_id)">Back to Device</x-action-button>
                        </div>
                    </x-info-panel>
                @endif

                <x-info-panel title="Result" description="Status akhir yang dikirim Windows Agent atau disiapkan dashboard.">
                    <dl class="space-y-4 text-sm">
                        <div>
                            <dt class="text-slate-500">Result Message</dt>
                            <dd class="mt-1 text-slate-900">{{ $remoteAction->result_message ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Error Message</dt>
                            <dd class="mt-1 text-slate-900">{{ $remoteAction->error_message ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Executed At</dt>
                            <dd class="mt-1 text-slate-900">{{ $remoteAction->executed_at?->format('Y-m-d H:i:s') ?? '-' }}</dd>
                        </div>
                    </dl>
                </x-info-panel>
            </div>

            <div class="space-y-5">
                <x-info-panel title="Confirmation" description="Restart wajib dikonfirmasi admin dan disertai alasan.">
                    <dl class="space-y-4 text-sm">
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-slate-500">Required</dt>
                            <dd class="font-medium text-slate-900">{{ $remoteAction->requires_confirmation ? 'Yes' : 'No' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-slate-500">Confirmed By</dt>
                            <dd class="font-medium text-slate-900">{{ $remoteAction->confirmer?->name ?? '-' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-slate-500">Confirmed At</dt>
                            <dd class="font-medium text-slate-900">{{ $remoteAction->confirmed_at?->format('Y-m-d H:i:s') ?? '-' }}</dd>
                        </div>
                    </dl>
                </x-info-panel>

                <x-info-panel title="Reason" description="Alasan admin yang tersimpan untuk audit.">
                    <p class="whitespace-pre-line text-sm text-slate-700">{{ $remoteAction->reason ?? '-' }}</p>
                </x-info-panel>

                <x-info-panel title="Payload" description="Payload aman tanpa credential Windows atau RDP.">
                    <pre class="max-h-80 overflow-auto rounded-md bg-slate-950 p-3 text-xs text-white">{{ json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                </x-info-panel>
            </div>
        </div>
    @endif
@endsection
