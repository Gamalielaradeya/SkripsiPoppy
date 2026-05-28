@extends('layouts.app')

@section('title', 'Incident Detail')
@section('description', 'Detail evidence dan status penanganan incident.')

@section('content')
    <x-empty-state
        title="Detail incident belum tersedia."
        message="Route siap untuk incident ID {{ $id }}, tetapi incident engine belum masuk Milestone 1."
    />
@endsection
