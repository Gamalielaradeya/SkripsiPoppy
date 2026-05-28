@extends('layouts.app')

@section('title', 'Dashboard')
@section('description', 'Ringkasan kondisi device Accurate, koneksi Firebird, audit, alert, dan incident.')

@section('content')
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <x-status-card title="Device Online" value="-" status="unknown" description="Menunggu data heartbeat dari Windows Agent." />
        <x-status-card title="Firebird Connectivity" value="-" status="unknown" description="Menunggu hasil koneksi client ke Firebird VPS:3051." />
        <x-status-card title="Accurate Active" value="-" status="unknown" description="Menunggu status proses accurate.exe dari device." />
        <x-status-card title="Open Alerts" value="-" status="unknown" description="Alert engine belum masuk Milestone 1." />
        <x-status-card title="Audit Today" value="-" status="unknown" description="Accurate Audit Reader belum masuk Milestone 1." />
        <x-status-card title="Open Incidents" value="-" status="unknown" description="Incident correlation belum masuk Milestone 1." />
    </div>

    <x-empty-state
        title="Belum ada data monitoring real-device."
        message="Dashboard ini sengaja tidak memakai data palsu. Data akan tampil setelah database monitoring, RSyslog parser, dan Windows Agent diimplementasikan pada milestone berikutnya."
    />
@endsection
