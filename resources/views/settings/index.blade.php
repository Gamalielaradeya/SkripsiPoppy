@extends('layouts.app')

@section('title', 'Pengaturan')
@section('description', 'Konfigurasi sistem pemantauan, notifikasi, audit Accurate, dan tindakan jarak jauh.')

@section('content')
    @php use App\Http\Controllers\SettingController; @endphp

    @if (session('status'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-900" id="settings-status">
            {{ session('status') }}
        </div>
        <meta http-equiv="refresh" content="1">
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var el = document.getElementById('settings-status');
                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        </script>
    @endif

    {{-- ═══ AMBANG PEMANTAUAN (Threshold) ═══ --}}
    <div class="mb-3 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-slate-950">Ambang Pemantauan</h2>
    </div>

    @php $thresholdGroups = $thresholdSettings->groupBy('group'); @endphp

    <form method="POST" action="{{ route('settings.thresholds.update') }}">
        @csrf
        @method('PUT')

        <div class="grid gap-5 lg:grid-cols-2">
            @foreach ($thresholdGroups as $group => $items)
                <x-info-panel :title="$groupLabels[$group] ?? ucfirst($group)" description="">
                    <div class="space-y-3">
                        @foreach ($items as $setting)
                            @php $label = $labels[$setting->key] ?? $setting->key; @endphp
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <label for="thr_{{ $setting->id }}" class="text-sm font-medium text-slate-700">{{ $label }}</label>
                                    <p class="text-xs text-slate-500">{{ $setting->description }}</p>
                                </div>
                                <input
                                    id="thr_{{ $setting->id }}"
                                    type="hidden"
                                    name="settings[{{ $loop->parent->index * 20 + $loop->index }}][key]"
                                    value="{{ $setting->key }}"
                                    readonly
                                >
                                <input
                                    name="settings[{{ $loop->parent->index * 20 + $loop->index }}][value]"
                                    value="{{ $setting->value }}"
                                    class="w-24 rounded-md border border-slate-200 px-2 py-1.5 font-mono text-xs text-slate-700 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100"
                                >
                            </div>
                        @endforeach
                    </div>
                </x-info-panel>
            @endforeach
        </div>

        <div class="mt-4">
            <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Simpan Ambang Pemantauan</button>
        </div>
    </form>

    {{-- ═══ PENGATURAN .ENV ═══ --}}
    <div class="mb-3 mt-8 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-slate-950">Pengaturan Sistem</h2>
        <span class="text-xs text-slate-500">Disimpan ke .env</span>
    </div>

    <form method="POST" action="{{ route('settings.env.update') }}">
        @csrf

        <div class="grid gap-5 lg:grid-cols-2">
            @foreach ($envSettings as $group => $items)
                <x-info-panel :title="$groupLabels[$group] ?? ucfirst($group)" description="">
                    <div class="space-y-3">
                        @foreach ($items as $setting)
                            <div
                                class="flex items-center justify-between gap-3"
                                @if ($setting['sensitive']) x-data="{ visible: false }" @endif
                            >
                                <div class="min-w-0 flex-1">
                                    <label class="text-sm font-medium text-slate-700">{{ $setting['label'] }}</label>
                                    <p class="text-xs text-slate-500">{{ $setting['description'] }}</p>
                                </div>

                                <input
                                    type="hidden"
                                    name="env[{{ $loop->parent->index * 20 + $loop->index }}][key]"
                                    value="{{ $setting['key'] }}"
                                >

                                @if ($setting['type'] === 'boolean')
                                    <select
                                        name="env[{{ $loop->parent->index * 20 + $loop->index }}][value]"
                                        class="w-28 rounded-md border border-slate-200 px-2 py-1.5 text-xs font-mono text-slate-700 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100"
                                    >
                                        <option value="true" @selected($setting['value'] === 'true')>Aktif</option>
                                        <option value="false" @selected($setting['value'] !== 'true')>Nonaktif</option>
                                    </select>
                                @elseif ($setting['sensitive'])
                                    <div class="flex items-center gap-1.5">
                                        <input
                                            :type="visible ? 'text' : 'password'"
                                            name="env[{{ $loop->parent->index * 20 + $loop->index }}][value]"
                                            value="{{ $setting['value'] }}"
                                            class="w-48 rounded-md border border-slate-200 px-2 py-1.5 font-mono text-xs text-slate-700 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100"
                                        >
                                        <button type="button" class="rounded p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600" x-on:click="visible = ! visible">
                                            <svg x-show="!visible" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <svg x-show="visible" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <input
                                        type="text"
                                        name="env[{{ $loop->parent->index * 20 + $loop->index }}][value]"
                                        value="{{ $setting['value'] }}"
                                        class="w-48 rounded-md border border-slate-200 px-2 py-1.5 font-mono text-xs text-slate-700 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100"
                                    >
                                @endif
                            </div>
                        @endforeach
                    </div>
                </x-info-panel>
            @endforeach
        </div>

        <div class="mt-4">
            <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Simpan Pengaturan Sistem</button>
        </div>
    </form>

    {{-- ═══ INFORMASI SISTEM ═══ --}}
    <div class="mt-8 grid gap-5 lg:grid-cols-3">
        <x-info-panel title="Informasi Aplikasi" description="Metadata sistem yang tidak dapat diubah di sini.">
            <dl class="space-y-3 text-sm">
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Nama Aplikasi</dt>
                    <dd class="font-medium text-slate-900">{{ config('app.name') }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Zona Waktu</dt>
                    <dd class="font-medium text-slate-900">Asia/Jakarta</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Environment</dt>
                    <dd class="font-medium text-slate-900">{{ app()->environment() }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Debug Mode</dt>
                    <dd class="font-medium text-slate-900">{{ config('app.debug') ? 'Aktif' : 'Nonaktif' }}</dd>
                </div>
                <div class="rounded-md bg-slate-50 p-3 text-xs text-slate-500">
                    Ubah nilai di atas melalui file .env atau deployment konfigurasi.
                </div>
            </dl>
        </x-info-panel>

        <x-info-panel title="ZeroTier" description="Jaringan privat untuk komunikasi VPS dan klien Windows.">
            <dl class="space-y-3 text-sm">
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Network ID</dt>
                    <dd class="font-mono text-xs font-medium text-slate-900">e4da7455b2b688af</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Interface VPS</dt>
                    <dd class="font-mono text-xs font-medium text-slate-900">ztwfumfxi5</dd>
                </div>
                <div class="rounded-md bg-slate-50 p-3 text-xs text-slate-500">
                    Konfigurasi ZeroTier dikelola di level sistem operasi, bukan melalui panel ini.
                </div>
            </dl>
        </x-info-panel>

        <x-info-panel title="Model Data" description="Jumlah record monitoring yang tersimpan.">
            <dl class="space-y-3 text-sm">
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Devices</dt>
                    <dd class="font-semibold text-slate-900">{{ \App\Models\Device::count() }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Alerts</dt>
                    <dd class="font-semibold text-slate-900">{{ \App\Models\Alert::count() }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Incidents</dt>
                    <dd class="font-semibold text-slate-900">{{ \App\Models\Incident::count() }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Log Entries</dt>
                    <dd class="font-semibold text-slate-900">{{ \App\Models\LogEntry::count() }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Audit Events</dt>
                    <dd class="font-semibold text-slate-900">{{ \App\Models\AccurateAuditEvent::count() }}</dd>
                </div>
            </dl>
        </x-info-panel>
    </div>
@endsection