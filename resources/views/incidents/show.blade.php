@extends('layouts.app')

@section('title', $incident?->title ?? 'Detail Incident')
@section('description', 'Detail masalah operasional, bukti, alert terkait, dan status penanganan.')

@section('content')
    @if (! $incident)
        <x-empty-state
            title="Detail incident tidak ditemukan."
            message="Tidak ada incident dengan ID {{ $id }}. Incident correlation logic belum diimplementasikan — data hanya dari seeder atau pembuatan manual."
        />
    @else
        <div class="space-y-5">
            {{-- Header --}}
            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <x-severity-badge :severity="$incident->severity" />
                            <x-status-badge :status="$incident->status" />
                            <span class="text-xs text-slate-500">{{ $incident->detected_at?->format('Y-m-d H:i:s') ?? '-' }}</span>
                            <span class="font-mono text-xs text-slate-400">{{ $incident->incident_code }}</span>
                        </div>
                        <h2 class="mt-3 text-xl font-semibold text-slate-950">{{ $incident->title }}</h2>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @if ($incident->status === 'open')
                            <form method="POST" action="{{ route('incidents.acknowledge', $incident) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center rounded-md border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                                    Akui
                                </button>
                            </form>
                        @endif

                        @if (in_array($incident->status, ['open', 'acknowledged'], true))
                            <form method="POST" action="{{ route('incidents.resolve', $incident) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center rounded-md bg-slate-900 px-3 py-2 text-sm font-medium text-white transition hover:bg-slate-800">
                                    Selesaikan
                                </button>
                            </form>
                        @endif

                        @if ($incident->device)
                            <a href="{{ route('devices.show', $incident->device) }}" class="inline-flex items-center justify-center rounded-md border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-sky-700 transition hover:bg-sky-50">
                                Buka Perangkat
                            </a>
                        @endif
                    </div>
                </div>
            </section>

            {{-- Ringkasan --}}
            <section class="grid gap-5 lg:grid-cols-2">
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Incident Info</h3>
                    <dl class="mt-4 space-y-4 text-sm">
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-slate-500">Target</dt>
                            <dd class="text-right font-medium text-slate-900">{{ $incident->target_name ?? '-' }}</dd>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-slate-500">Target Type</dt>
                            <dd class="text-right font-medium text-slate-900">{{ $incident->target_type ?? '-' }}</dd>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-slate-500">Code</dt>
                            <dd class="text-right font-mono text-xs font-medium text-slate-900">{{ $incident->incident_code }}</dd>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-slate-500">Severity</dt>
                            <dd class="text-right"><x-severity-badge :severity="$incident->severity" /></dd>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-slate-500">Status</dt>
                            <dd class="text-right"><x-status-badge :status="$incident->status" /></dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Timeline</h3>
                    <dl class="mt-4 space-y-4 text-sm">
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-slate-500">Detected</dt>
                            <dd class="text-right font-medium text-slate-900">{{ $incident->detected_at?->format('Y-m-d H:i:s') ?? '-' }}</dd>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-slate-500">Duration</dt>
                            <dd class="text-right font-medium text-slate-900">
                                @if ($incident->detected_at)
                                    @if ($incident->status === 'resolved' && $incident->resolved_at)
                                        {{ $incident->detected_at->diffForHumans($incident->resolved_at, ['parts' => 2]) }}
                                    @else
                                        {{ $incident->detected_at->diffForHumans(now(), ['parts' => 2]) }}
                                    @endif
                                @else
                                    -
                                @endif
                            </dd>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-slate-500">Acknowledged</dt>
                            <dd class="text-right font-medium text-slate-900">{{ $incident->acknowledged_at?->format('Y-m-d H:i:s') ?? '-' }}</dd>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-slate-500">Resolved</dt>
                            <dd class="text-right font-medium text-slate-900">{{ $incident->resolved_at?->format('Y-m-d H:i:s') ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </section>

            {{-- Ringkasan Masalah --}}
            @if ($incident->summary)
                <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Summary</h3>
                    <p class="mt-4 text-sm leading-6 text-slate-700">{{ $incident->summary }}</p>
                </section>
            @endif

            {{-- Evidence --}}
            @if (! empty($incident->evidence_json))
                <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Evidence</h3>
                        <span class="text-xs text-slate-500">{{ count($incident->evidence_json) }} item(s)</span>
                    </div>

                    <div class="overflow-hidden rounded-lg border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Key</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Value</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach ($incident->evidence_json as $key => $value)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-slate-700">{{ $key }}</td>
                                        <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_SLASHES) : $value }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @else
                <section class="rounded-lg border border-dashed border-slate-300 bg-white p-5 shadow-sm">
                    <div class="text-center">
                        <div class="mx-auto mb-3 h-1 w-12 rounded-full bg-slate-200"></div>
                        <h3 class="text-sm font-semibold text-slate-500">No evidence</h3>
                        <p class="mt-1 text-xs text-slate-400">This incident has no measurement evidence attached.</p>
                    </div>
                </section>
            @endif

            {{-- Alert Terkait --}}
            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Related Alerts</h3>
                    <span class="text-xs text-slate-500">{{ $incident->alerts->count() }} alert(s)</span>
                </div>

                @if ($incident->alerts->isEmpty())
                    <div class="text-center py-4">
                        <p class="text-sm text-slate-500">No alerts are linked to this incident.</p>
                        <p class="mt-1 text-xs text-slate-400">Incident correlation will auto-link alerts when detection runs.</p>
                    </div>
                @else
                    <div class="overflow-hidden rounded-lg border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Time</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Alert Title</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Severity</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach ($incident->alerts as $alert)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-3 text-slate-600">{{ $alert->detected_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                        <td class="px-4 py-3 font-medium text-slate-900">{{ $alert->title }}</td>
                                        <td class="px-4 py-3"><x-severity-badge :severity="$alert->severity" /></td>
                                        <td class="px-4 py-3"><x-status-badge :status="$alert->status" /></td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('alerts.show', $alert) }}" class="text-sm font-medium text-sky-700 hover:text-sky-900">Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            {{-- Perangkat Terkait --}}
            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Related Device</h3>
                @if ($incident->device)
                    <div class="mt-4 flex items-center justify-between">
                        <div>
                            <a href="{{ route('devices.show', $incident->device) }}" class="font-medium text-sky-700 hover:text-sky-800">{{ $incident->device->display_name }}</a>
                            <div class="mt-1 text-xs text-slate-500">{{ $incident->device->hostname }} / {{ $incident->device->windows_user ?: '-' }} / {{ $incident->device->ip_zerotier ?: '-' }}</div>
                        </div>
                        <x-status-badge :status="$incident->device->display_status" />
                    </div>
                @else
                    <p class="mt-4 text-sm text-slate-500">No device associated with this incident.</p>
                @endif
            </section>
        </div>
    @endif
@endsection