@extends('layouts.app')

@section('title', 'Advanced Logs')
@section('description', 'Raw dan parsed logs dari RSyslog untuk investigasi teknis.')

@section('content')
    <x-empty-state
        title="Belum ada log teknis."
        message="Advanced Logs akan berisi raw log setelah RSyslog parser diimplementasikan. Dashboard utama tetap tidak menjadi raw log viewer."
    />
@endsection
