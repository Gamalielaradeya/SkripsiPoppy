@extends('layouts.app')

@section('title', 'Alert Detail')
@section('description', 'Detail alert, evidence, impact, recommended action, dan riwayat notifikasi.')

@section('content')
    <x-empty-state
        title="Detail alert belum tersedia."
        message="Route siap untuk alert ID {{ $id }}, tetapi alert detection belum diimplementasikan."
    />
@endsection
