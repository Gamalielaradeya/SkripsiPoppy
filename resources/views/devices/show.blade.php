@extends('layouts.app')

@section('title', 'Device Detail')
@section('description', 'Detail satu device Windows, status telemetry, koneksi Firebird, Accurate process, dan action manual.')

@section('content')
    @if (! $device)
        <x-empty-state
            title="Device tidak ditemukan."
            message="Tidak ada device real dengan ID {{ $id }}. Device harus dibuat dari identitas agent_id, bukan hostname."
        />
    @else
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-950">{{ $device->display_name }}</h2>
                    <p class="mt-1 font-mono text-xs text-slate-500">{{ $device->agent_id }}</p>
                </div>
                <x-status-badge :status="$device->status" />
            </div>

            <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-2 xl:grid-cols-4">
                <div>
                    <dt class="text-slate-500">Hostname</dt>
                    <dd class="mt-1 font-medium text-slate-900">{{ $device->hostname }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Windows User</dt>
                    <dd class="mt-1 font-medium text-slate-900">{{ $device->windows_user ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">ZeroTier IP</dt>
                    <dd class="mt-1 font-medium text-slate-900">{{ $device->ip_zerotier ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Last Seen</dt>
                    <dd class="mt-1 font-medium text-slate-900">{{ $device->last_seen_at?->diffForHumans() ?? '-' }}</dd>
                </div>
            </dl>
        </section>
    @endif
@endsection
