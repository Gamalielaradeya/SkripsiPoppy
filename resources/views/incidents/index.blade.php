@extends('layouts.app')

@section('title', 'Incidents')
@section('description', 'Korelasi masalah operasional berdasarkan beberapa evidence.')

@section('content')
    <x-empty-state
        title="Belum ada incident."
        message="Incident correlation belum diimplementasikan. Incident akan dibuat hanya jika evidence dan rule sudah jelas."
    />
@endsection
