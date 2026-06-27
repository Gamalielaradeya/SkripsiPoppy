@extends('layouts.app')

@section('title', $alert?->title ?? 'Detail Alert')
@section('description', 'Detail alert, bukti, dampak, tindakan yang disarankan, dan riwayat notifikasi.')

@section('content')
    @if (! $alert)
        <x-empty-state
            title="Detail alert belum tersedia."
            message="Route siap untuk alert ID {{ $id }}, tetapi alert tersebut belum ada."
        />
    @else
        <div class="space-y-5">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <x-severity-badge :severity="$alert->severity" />
                        <x-status-badge :status="$alert->status" />
                        <span class="text-xs text-slate-500">{{ $alert->detected_at?->format('Y-m-d H:i:s') ?? '-' }}</span>
                    </div>
                    <h2 class="mt-3 text-xl font-semibold text-slate-950">{{ $alert->title }}</h2>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">{{ $alert->description }}</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    @if ($alert->status === 'open')
                        <form method="POST" action="{{ route('alerts.acknowledge', $alert) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center rounded-md border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                                Acknowledge
                            </button>
                        </form>
                    @endif

                    @if (in_array($alert->status, ['open', 'acknowledged'], true))
                        <form method="POST" action="{{ route('alerts.resolve', $alert) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center rounded-md bg-slate-900 px-3 py-2 text-sm font-medium text-white transition hover:bg-slate-800">
                                Resolve
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="mt-6 grid gap-4 text-sm md:grid-cols-2 xl:grid-cols-4">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Target</div>
                    <div class="mt-1 font-medium text-slate-950">{{ $alert->target_name }}</div>
                    <div class="text-xs text-slate-500">{{ $alert->target_type }} / {{ $alert->target_id ?: '-' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Terdeteksi Oleh</div>
                    <div class="mt-1 font-medium text-slate-950">{{ $alert->detected_by }}</div>
                    <div class="text-xs text-slate-500">{{ $alert->source ?: '-' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Aturan</div>
                    <div class="mt-1 font-medium text-slate-950">{{ $alert->alert_code }}</div>
                    <div class="text-xs text-slate-500">{{ $alert->category }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Riwayat</div>
                    <div class="mt-1 text-slate-700">Pertama: {{ $alert->first_detected_at?->format('Y-m-d H:i:s') ?? '-' }}</div>
                    <div class="text-xs text-slate-500">Terakhir: {{ $alert->last_detected_at?->format('Y-m-d H:i:s') ?? '-' }}</div>
                </div>
            </div>
        </section>

        <section class="grid gap-5 lg:grid-cols-2">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Ringkasan Bukti</h3>
                <p class="mt-3 text-sm leading-6 text-slate-700">{{ $alert->evidence_summary ?: '-' }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Dampak</h3>
                <p class="mt-3 text-sm leading-6 text-slate-700">{{ $alert->impact }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Tindakan yang Disarankan</h3>
                <p class="mt-3 text-sm leading-6 text-slate-700">{{ $alert->recommended_action }}</p>
            </div>
        </section>

        <section>
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Baris Bukti</h3>
                <span class="text-xs text-slate-500">{{ $alert->evidences->count() }} baris</span>
            </div>
            <x-data-table>
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Kunci</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Nilai</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Tipe</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Sumber</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Diukur Pada</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($alert->evidences as $evidence)
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $evidence->evidence_key }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $evidence->evidence_value ?: '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $evidence->evidence_type }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $evidence->source ?: '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $evidence->measured_at?->format('Y-m-d H:i:s') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-500">Tidak ada bukti tersimpan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-data-table>
        </section>

        <section>
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Riwayat Notifikasi</h3>
                <span class="text-xs text-slate-500">{{ $alert->notifications->count() }} percobaan</span>
            </div>
            <x-data-table>
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Kanal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Penerima</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Dikirim Pada</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Kesalahan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($alert->notifications as $notification)
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $notification->channel }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$notification->status" /></td>
                            <td class="px-4 py-3 text-slate-600">{{ $notification->recipient ?: '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $notification->sent_at?->format('Y-m-d H:i:s') ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $notification->error_message ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-500">Tidak ada percobaan notifikasi tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-data-table>
        </section>

        <section class="grid gap-5 lg:grid-cols-3">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Perangkat Terkait</h3>
                @if ($alert->device)
                    <a href="{{ route('devices.show', $alert->device) }}" class="mt-3 block font-medium text-sky-700 hover:text-sky-800">{{ $alert->device->display_name }}</a>
                    <div class="mt-1 text-sm text-slate-600">{{ $alert->device->hostname }} / {{ $alert->device->windows_user ?: '-' }}</div>
                @else
                    <p class="mt-3 text-sm text-slate-500">Tidak ada relasi perangkat.</p>
                @endif
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Audit Accurate Terkait</h3>
                @if ($alert->accurateAuditEvent)
                    <a href="{{ route('accurate-audit.show', $alert->accurateAuditEvent) }}" class="mt-3 block font-medium text-sky-700 hover:text-sky-800">Audit #{{ $alert->accurateAuditEvent->accurate_audit_id }}</a>
                    <div class="mt-1 text-sm text-slate-600">{{ $alert->accurateAuditEvent->transaction_type ?: '-' }} / {{ $alert->accurateAuditEvent->accurate_username ?: '-' }}</div>
                @else
                    <p class="mt-3 text-sm text-slate-500">Tidak ada relasi audit Accurate.</p>
                @endif
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Log Terkait</h3>
                @if ($alert->log)
                    <a href="{{ route('advanced-logs.show', $alert->log) }}" class="mt-3 block font-medium text-sky-700 hover:text-sky-800">Log #{{ $alert->log->id }}</a>
                    <div class="mt-1 text-sm text-slate-600">{{ $alert->log->source ?: '-' }} / {{ $alert->log->event_type ?: '-' }}</div>
                @else
                    <p class="mt-3 text-sm text-slate-500">Tidak ada relasi log mentah.</p>
                @endif
            </div>
        </section>
        </div>
    @endif
@endsection