@extends('layouts.app')

@section('title', 'Device Detail')
@section('description', 'Detail satu device Windows, status telemetry, koneksi Firebird, Accurate process, dan action manual.')

@section('content')
    <x-empty-state
        title="Detail device belum tersedia."
        message="Route siap untuk device ID {{ $id }}, tetapi data device dan remote action belum diimplementasikan pada Milestone 1."
    />
@endsection
