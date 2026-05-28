@extends('layouts.app')

@section('title', 'Remote Action Detail')
@section('description', 'Detail tindakan remote manual dan hasil dari Windows Agent.')

@section('content')
    <x-empty-state
        title="Detail remote action belum tersedia."
        message="Route siap untuk remote action ID {{ $id }}, tetapi command polling belum masuk Milestone 1."
    />
@endsection
