@extends('layouts.app')

@section('title', 'Dashboard')
@section('description', 'Ringkasan kondisi device Accurate, koneksi Firebird, audit, alert, dan incident.')

@section('content')
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <x-status-card title="Device Online" :value="$onlineDevices . ' / ' . $totalDevices" status="unknown" description="Menunggu data heartbeat real-device dari Windows Agent." />
        <x-status-card title="Firebird Connectivity" :value="$firebirdConnectedDevices . ' / ' . $totalDevices" status="unknown" description="Menunggu hasil koneksi device ke Firebird Accurate." />
        <x-status-card title="Accurate Active" :value="$accurateRunningDevices . ' / ' . $totalDevices" status="unknown" description="Menunggu status proses accurate.exe dari device." />
        <x-status-card title="Open Alerts" :value="$openAlerts" status="unknown" description="Diambil dari tabel alerts; tidak ada data palsu." />
        <x-status-card title="Audit Today" :value="$auditEventsToday" status="unknown" description="Diambil dari tabel accurate_audit_events; sync belum diimplementasikan." />
        <x-status-card title="Open Incidents" :value="$openIncidents" status="unknown" description="Diambil dari tabel incidents; korelasi belum diimplementasikan." />
    </div>

    @if ($totalDevices === 0)
        <x-empty-state
            title="Belum ada data monitoring real-device."
            message="Dashboard ini sengaja tidak memakai data palsu. Data akan tampil setelah Windows Agent dan parser menyimpan status berdasarkan agent_id."
        />
    @endif
@endsection
