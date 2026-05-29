@extends('layouts.app')

@section('title', 'Accurate Audit Detail')
@section('description', 'Detail event audit Accurate yang sudah disinkronkan dari Firebird AUDIT + USERS.')

@section('content')
    @if (! $auditEvent)
        <x-empty-state
            title="Audit event belum tersedia."
            message="Tidak ada event audit tersimpan untuk ID {{ $id }}. Data detail hanya berasal dari hasil sync Firebird AUDIT + USERS."
        />
    @else
        <div class="flex items-center justify-between">
            <a href="{{ route('accurate-audit.index') }}" class="text-sm font-medium text-sky-700 hover:text-sky-900">Back to Accurate Audit</a>
            <span class="font-mono text-xs text-slate-500">AUDITID {{ $auditEvent->accurate_audit_id }}</span>
        </div>

        <div class="grid gap-5 lg:grid-cols-2">
            <x-info-panel title="Audit Event" description="Aktivitas internal Accurate dari tabel AUDIT.">
                <dl class="space-y-3 text-sm">
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-slate-500">Audit time</dt>
                        <dd class="text-right font-medium text-slate-900">{{ $auditEvent->activity_time?->format('Y-m-d H:i:s') ?? '-' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-slate-500">Accurate user</dt>
                        <dd class="text-right font-medium text-slate-900">{{ $auditEvent->accurate_username ?? '-' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-slate-500">Full name</dt>
                        <dd class="text-right font-medium text-slate-900">{{ $auditEvent->accurate_fullname ?? '-' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-slate-500">Source / module</dt>
                        <dd class="text-right font-medium text-slate-900">{{ $auditEvent->source ?? '-' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-slate-500">Transaction type</dt>
                        <dd class="text-right font-medium text-slate-900">{{ $auditEvent->transaction_type ?? '-' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-slate-500">Reference / invoice</dt>
                        <dd class="text-right font-medium text-slate-900">{{ $auditEvent->invoice_no ?? '-' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-slate-500">Status</dt>
                        <dd class="text-right font-medium text-slate-900">{{ $auditEvent->status ?? '-' }}</dd>
                    </div>
                </dl>
            </x-info-panel>

            <x-info-panel title="Nullable POC Fields" description="COMP_NAME dan IPADDRESS bisa kosong pada data Accurate.">
                <dl class="space-y-3 text-sm">
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-slate-500">COMP_NAME</dt>
                        <dd class="text-right font-medium text-slate-900">{{ $auditEvent->comp_name ?? 'Tidak tersedia' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-slate-500">IPADDRESS</dt>
                        <dd class="text-right font-medium text-slate-900">{{ $auditEvent->ip_address ?? 'Tidak tersedia' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-slate-500">App version</dt>
                        <dd class="text-right font-medium text-slate-900">{{ $auditEvent->app_version ?? '-' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-slate-500">Synced at</dt>
                        <dd class="text-right font-medium text-slate-900">{{ $auditEvent->synced_at?->format('Y-m-d H:i:s') ?? '-' }}</dd>
                    </div>
                    <div class="rounded-md bg-slate-50 p-3 text-xs text-slate-600">
                        Windows user dan IP aktif tetap berasal dari Windows Agent, bukan dari audit Firebird.
                    </div>
                </dl>
            </x-info-panel>
        </div>

        <x-info-panel title="Transaction Description" description="Deskripsi dari AUDIT.TRANSDESCRIPTION jika tersedia.">
            <p class="text-sm leading-6 text-slate-700">{{ $auditEvent->transaction_description ?? '-' }}</p>
        </x-info-panel>

        <x-info-panel title="Raw Payload" description="Payload tersimpan untuk debugging schema Firebird tanpa mengubah database Accurate.">
            <pre class="max-h-96 overflow-auto rounded-md bg-slate-950 p-4 text-xs leading-5 text-slate-100">{{ json_encode($auditEvent->raw_payload ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
        </x-info-panel>
    @endif
@endsection
