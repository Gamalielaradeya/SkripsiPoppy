@extends('layouts.app')

@section('title', 'Incidents')
@section('description', 'Korelasi masalah operasional berdasarkan beberapa evidence.')

@section('content')
    <x-filter-panel description="Filter visual untuk incident type, target, severity, dan status. Correlation logic belum diimplementasikan.">
        <div class="grid gap-3 md:grid-cols-5">
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Incident Type</span>
                <input disabled class="mt-1 w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-400" placeholder="Code/type">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Target Device</span>
                <input disabled class="mt-1 w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-400" placeholder="Device/server">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Severity</span>
                <select disabled class="mt-1 w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-400">
                    <option>All severity</option>
                </select>
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Status</span>
                <select disabled class="mt-1 w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-400">
                    <option>Open / resolved</option>
                </select>
            </label>
            <div class="flex items-end">
                <x-action-button disabled class="w-full">Apply Filter</x-action-button>
            </div>
        </div>
    </x-filter-panel>

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
                            <th class="px-4 py-3">Incident Type</th>
                            <th class="px-4 py-3">Target Device</th>
                            <th class="px-4 py-3">Severity</th>
                            <th class="px-4 py-3">Evidence Summary</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach ($incidents as $incident)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <a href="{{ route('incidents.show', $incident) }}" class="font-medium text-slate-950 hover:text-sky-700">{{ $incident->title }}</a>
                                    <div class="text-xs text-slate-500">{{ $incident->incident_code }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $incident->target_name ?? '-' }}</td>
                                <td class="px-4 py-3"><x-severity-badge :severity="$incident->severity" /></td>
                                <td class="px-4 py-3">
                                    {{ $incident->summary ?: ($incident->alerts->count().' linked alert(s)') }}
                                </td>
                                <td class="px-4 py-3"><x-status-badge :status="$incident->status" /></td>
                                <td class="px-4 py-3 text-right">
                                    <x-action-button :href="route('incidents.show', $incident)" variant="secondary">Detail</x-action-button>
                                </td>
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
