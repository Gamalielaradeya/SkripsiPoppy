@extends('layouts.app')

@section('title', 'Remote Actions')
@section('description', 'Riwayat tindakan remote manual oleh administrator.')

@section('content')
    <x-empty-state
        title="Belum ada tindakan remote."
        message="Remote Desktop dan Remote Restart belum diimplementasikan. Restart nanti wajib manual, confirmed, reasoned, dan audited."
    />
@endsection
