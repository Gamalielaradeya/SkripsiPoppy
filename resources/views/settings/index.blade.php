@extends('layouts.app')

@section('title', 'Settings')
@section('description', 'Konfigurasi threshold, Telegram, Firebird, Agent, dan Remote Actions.')

@section('content')
    <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
        Settings pada milestone ini bersifat read-only UI foundation. Nilai sensitif selalu dimask dan tidak ada secret yang ditampilkan.
    </div>

    <div class="grid gap-5 lg:grid-cols-2">
        <x-info-panel title="General" description="Identitas aplikasi, timezone, dan refresh dashboard.">
            <dl class="space-y-3 text-sm">
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Application</dt>
                    <dd class="font-medium text-slate-900">{{ config('app.name') }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Timezone</dt>
                    <dd class="font-medium text-slate-900">{{ config('app.timezone') }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Dashboard refresh</dt>
                    <dd class="font-medium text-slate-900">Manual / browser reload</dd>
                </div>
            </dl>
        </x-info-panel>

        <x-info-panel title="Device Monitoring" description="Heartbeat dan agent lifecycle.">
            <div class="space-y-3 text-sm">
                @forelse ($thresholdSettings->where('group', 'device') as $setting)
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="font-medium text-slate-900">{{ $setting->key }}</div>
                            <div class="text-xs text-slate-500">{{ $setting->description }}</div>
                        </div>
                        <div class="font-mono text-xs text-slate-700">{{ $setting->value }}</div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada threshold device tersimpan.</p>
                @endforelse
            </div>
        </x-info-panel>

        <x-info-panel title="Thresholds" description="Batas CPU, RAM, disk, Firebird latency, dan cooldown.">
            <div class="space-y-3 text-sm">
                @forelse ($thresholdSettings->where('group', '!=', 'device') as $setting)
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="font-medium text-slate-900">{{ $setting->key }}</div>
                            <div class="text-xs text-slate-500">{{ $setting->description }}</div>
                        </div>
                        <div class="font-mono text-xs text-slate-700">{{ $setting->value }}</div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada threshold tambahan tersimpan.</p>
                @endforelse
            </div>
        </x-info-panel>

        <x-info-panel title="Telegram" description="Notifikasi alert kontekstual. Token tidak ditampilkan.">
            <dl class="space-y-3 text-sm">
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Bot token</dt>
                    <dd class="font-mono text-xs text-slate-700">************</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Chat ID</dt>
                    <dd class="font-mono text-xs text-slate-700">masked</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Status</dt>
                    <dd><x-status-badge status="unknown" /></dd>
                </div>
            </dl>
        </x-info-panel>

        <x-info-panel title="Accurate Audit" description="Firebird AUDIT + USERS read-only sync settings. Secrets are masked.">
            <dl class="space-y-3 text-sm">
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Firebird host</dt>
                    <dd class="font-mono text-xs text-slate-700">configured in environment/source</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Database path</dt>
                    <dd class="font-mono text-xs text-slate-700">masked</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Read-only password</dt>
                    <dd class="font-mono text-xs text-slate-700">************</dd>
                </div>
            </dl>
        </x-info-panel>

        <x-info-panel title="Remote Actions" description="Manual remote desktop/restart policy. Restart runs only through authorized agent polling.">
            <dl class="space-y-3 text-sm">
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Remote restart</dt>
                    <dd><x-status-badge status="available" /></dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Reason required</dt>
                    <dd class="font-medium text-slate-900">Yes</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Confirmation required</dt>
                    <dd class="font-medium text-slate-900">Yes</dd>
                </div>
            </dl>
        </x-info-panel>
    </div>

    @if ($systemSettings->isNotEmpty())
        <x-info-panel title="Stored System Settings" description="Existing non-sensitive system settings from database. Sensitive values remain masked.">
            <div class="grid gap-3 text-sm md:grid-cols-2">
                @foreach ($systemSettings as $setting)
                    <div class="rounded-md border border-slate-200 p-3">
                        <div class="font-medium text-slate-900">{{ $setting->group }} / {{ $setting->key }}</div>
                        <div class="mt-1 font-mono text-xs text-slate-600">{{ $setting->is_sensitive ? '************' : ($setting->value ?? '-') }}</div>
                    </div>
                @endforeach
            </div>
        </x-info-panel>
    @endif
@endsection
