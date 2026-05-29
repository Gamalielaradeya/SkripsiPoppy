@extends('layouts.app')

@section('title', 'Incidents')
@section('description', 'Korelasi masalah operasional berdasarkan beberapa evidence.')

@section('content')
    @if ($incidents->isEmpty())
        <x-empty-state
            title="Belum ada incident."
            message="Incident correlation belum diimplementasikan. Incident akan dibuat hanya jika beberapa alert punya hubungan operasional yang jelas."
        />
    @else
        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Detected</th>
                            <th class="px-4 py-3">Severity</th>
                            <th class="px-4 py-3">Incident</th>
                            <th class="px-4 py-3">Target</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach ($incidents as $incident)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">{{ $incident->detected_at?->diffForHumans() ?? '-' }}</td>
                                <td class="px-4 py-3"><x-severity-badge :severity="$incident->severity" /></td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('incidents.show', $incident) }}" class="font-medium text-slate-950 hover:text-sky-700">{{ $incident->title }}</a>
                                    <div class="text-xs text-slate-500">{{ $incident->incident_code }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $incident->target_name }}</td>
                                <td class="px-4 py-3"><x-status-badge :status="$incident->status" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-4 py-3">
                {{ $incidents->links() }}
            </div>
        </section>
    @endif
@endsection
