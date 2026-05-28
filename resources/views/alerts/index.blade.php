@extends('layouts.app')

@section('title', 'Alerts')
@section('description', 'Peringatan kontekstual dengan target, evidence, impact, dan recommended action.')

@section('content')
    <x-empty-state
        title="Belum ada alert aktif."
        message="Alert engine dan Telegram belum diimplementasikan. Milestone 1 hanya menyiapkan halaman dan komponen UI."
    />
@endsection
