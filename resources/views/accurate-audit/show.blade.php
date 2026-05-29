@extends('layouts.app')

@section('title', 'Accurate Audit Detail')
@section('description', 'Detail event audit Accurate yang sudah disinkronkan dari Firebird.')

@section('content')
    <x-empty-state
        title="Detail audit belum tersedia."
        message="Route siap untuk audit ID {{ $id }}, tetapi sinkronisasi Firebird belum diimplementasikan."
    />
@endsection
