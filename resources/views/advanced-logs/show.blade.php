@extends('layouts.app')

@section('title', 'Advanced Log Detail')
@section('description', 'Detail raw message, parsed fields, source file, dan hash log.')

@section('content')
    @if (! $log)
        <x-empty-state
            title="Log tidak ditemukan."
            message="Tidak ada baris log dengan ID {{ $id }}. Advanced Logs hanya menampilkan data nyata dari RSyslog parser."
        />
    @else
        <div class="space-y-4">
        <section class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid gap-4 md:grid-cols-4">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Logged At</div>
                    <div class="mt-1 text-sm text-slate-800">{{ $log->logged_at?->format('Y-m-d H:i:s') ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Hostname</div>
                    <div class="mt-1 text-sm text-slate-800">{{ $log->hostname ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Source</div>
                    <div class="mt-1 text-sm text-slate-800">{{ $log->source ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Severity</div>
                    <div class="mt-1"><x-severity-badge :severity="$log->severity" /></div>
                </div>
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900">Raw Message</h2>
            <pre class="mt-3 overflow-x-auto rounded-md bg-slate-950 p-3 text-xs text-slate-100">{{ $log->raw_message }}</pre>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900">Parsed Payload</h2>
            <pre class="mt-3 overflow-x-auto rounded-md bg-slate-50 p-3 text-xs text-slate-700">{{ json_encode($log->parsed_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <dl class="grid gap-3 text-sm md:grid-cols-2">
                <div>
                    <dt class="font-semibold text-slate-500">Source File</dt>
                    <dd class="mt-1 font-mono text-xs text-slate-700">{{ $log->source_file ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-500">Hash</dt>
                    <dd class="mt-1 font-mono text-xs text-slate-700">{{ $log->hash }}</dd>
                </div>
            </dl>
        </section>
        </div>
    @endif
@endsection
