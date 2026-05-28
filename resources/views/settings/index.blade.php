@extends('layouts.app')

@section('title', 'Settings')
@section('description', 'Konfigurasi threshold, Telegram, Firebird, Agent, dan Remote Actions.')

@section('content')
    <x-empty-state
        title="Settings belum aktif."
        message="Halaman settings disiapkan sebagai placeholder. Penyimpanan threshold dan konfigurasi sensitif masuk milestone database dan integrasi."
    />
@endsection
