@extends('layouts.app')

@section('title', 'Advanced Log Detail')
@section('description', 'Detail raw message, parsed fields, source file, dan hash log.')

@section('content')
    <x-empty-state
        title="Detail log belum tersedia."
        message="Route siap untuk log ID {{ $id }}, tetapi parser dan tabel log belum masuk Milestone 1."
    />
@endsection
