@extends('layouts.app')

@section('title', 'Accurate Audit')
@section('description', 'Audit trail Accurate dari Firebird AUDIT + USERS secara read-only.')

@section('content')
    <x-empty-state
        title="Belum ada data audit Accurate."
        message="Accurate Audit Reader belum diimplementasikan. Halaman ini tidak memakai tabel LOGIN dan tidak menampilkan data palsu."
    />
@endsection
