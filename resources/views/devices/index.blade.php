@extends('layouts.app')

@section('title', 'Devices')
@section('description', 'Daftar laptop Windows pengguna Accurate 5 yang terhubung ke sistem monitoring.')

@section('content')
    <x-empty-state
        title="Belum ada device terdaftar."
        message="Device akan muncul setelah Windows Agent mengirim heartbeat dan parser menyimpan data berdasarkan agent_id."
    />
@endsection
