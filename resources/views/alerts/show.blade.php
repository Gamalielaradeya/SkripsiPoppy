@extends('layouts.app')

@section('title', $alert?->title ?? 'Alert Detail')
@section('description', 'Detail alert, evidence, impact, recommended action, dan riwayat notifikasi.')

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
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Detected By</div>
                    <div class="mt-1 font-medium text-slate-950">{{ $alert->detected_by }}</div>
                    <div class="text-xs text-slate-500">{{ $alert->source ?: '-' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Rule</div>
                    <div class="mt-1 font-medium text-slate-950">{{ $alert->alert_code }}</div>
                    <div class="text-xs text-slate-500">{{ $alert->category }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Lifecycle</div>
                    <div class="mt-1 text-slate-700">First: {{ $alert->first_detected_at?->format('Y-m-d H:i:s') ?? '-' }}</div>
                    <div class="text-xs text-slate-500">Last: {{ $alert->last_detected_at?->format('Y-m-d H:i:s') ?? '-' }}</div>
                </div>
            </div>
        </section>

        <section class="grid gap-5 lg:grid-cols-2">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Evidence Summary</h3>
                <p class="mt-3 text-sm leading-6 text-slate-700">{{ $alert->evidence_summary ?: '-' }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Impact</h3>
                <p class="mt-3 text-sm leading-6 text-slate-700">{{ $alert->impact }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Recommended Action</h3>
                <p class="mt-3 text-sm leading-6 text-slate-700">{{ $alert->recommended_action }}</p>
            </div>
        </section>

        <section>
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Evidence Rows</h3>
                <span class="text-xs text-slate-500">{{ $alert->evidences->count() }} row(s)</span>
            </div>
            <x-data-table>
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Key</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Value</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Source</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Measured At</th>
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
                            <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-500">No evidence stored.</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-data-table>
        </section>

        <section>
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Notification History</h3>
                <span class="text-xs text-slate-500">{{ $alert->notifications->count() }} attempt(s)</span>
            </div>
            <x-data-table>
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Channel</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Recipient</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Sent At</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Error</th>
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
                            <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-500">No notification attempt recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-data-table>
        </section>

        <section class="grid gap-5 lg:grid-cols-3">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Related Device</h3>
                @if ($alert->device)
                    <a href="{{ route('devices.show', $alert->device) }}" class="mt-3 block font-medium text-sky-700 hover:text-sky-800">{{ $alert->device->display_name }}</a>
                    <div class="mt-1 text-sm text-slate-600">{{ $alert->device->hostname }} / {{ $alert->device->windows_user ?: '-' }}</div>
                @else
                    <p class="mt-3 text-sm text-slate-500">No device relation.</p>
                @endif
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Related Accurate Audit</h3>
                @if ($alert->accurateAuditEvent)
                    <a href="{{ route('accurate-audit.show', $alert->accurateAuditEvent) }}" class="mt-3 block font-medium text-sky-700 hover:text-sky-800">Audit #{{ $alert->accurateAuditEvent->accurate_audit_id }}</a>
                    <div class="mt-1 text-sm text-slate-600">{{ $alert->accurateAuditEvent->transaction_type ?: '-' }} / {{ $alert->accurateAuditEvent->accurate_username ?: '-' }}</div>
                @else
                    <p class="mt-3 text-sm text-slate-500">No Accurate audit relation.</p>
                @endif
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Related Log</h3>
                @if ($alert->log)
                    <a href="{{ route('advanced-logs.show', $alert->log) }}" class="mt-3 block font-medium text-sky-700 hover:text-sky-800">Log #{{ $alert->log->id }}</a>
                    <div class="mt-1 text-sm text-slate-600">{{ $alert->log->source ?: '-' }} / {{ $alert->log->event_type ?: '-' }}</div>
                @else
                    <p class="mt-3 text-sm text-slate-500">No raw log relation.</p>
                @endif
            </div>
        </section>
        </div>
    @endif
@endsection
