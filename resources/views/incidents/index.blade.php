@extends('layouts.app')

@section('title', 'Incidents')
@section('description', 'Korelasi masalah operasional berdasarkan beberapa evidence.')

@section('content')
    <x-filter-panel description="Filter incident berdasarkan severity, status, dan target device.">
            <form method="GET" action="{{ route('incidents.index') }}" class="grid gap-3 md:grid-cols-5">
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Severity</span>
                    <select name="severity" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                        <option value="">All severity</option>
                        <option value="warning" @selected(request('severity') === 'warning')>Warning</option>
                        <option value="error" @selected(request('severity') === 'error')>Error</option>
                        <option value="critical" @selected(request('severity') === 'critical')>Critical</option>
                    </select>
                </label>
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Target Device</span>
                    <x-autocomplete-input name="target" :value="request('target')" placeholder="Device/server" :endpoint="route('search.incident-targets')" label="Target Device" />
                </label>
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Incident Type</span>
                    <x-autocomplete-input name="incident_type" :value="request('incident_type')" placeholder="Code/type" :endpoint="route('search.incident-types')" label="Incident Type" />
                </label>
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Status</span>
                    <select name="status" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                        <option value="">All</option>
                        <option value="open" @selected(request('status') === 'open')>Open</option>
                        <option value="resolved" @selected(request('status') === 'resolved')>Resolved</option>
                    </select>
                </label>
                <div class="flex items-end gap-2">
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-slate-900 px-3 py-2 text-sm font-medium text-white transition hover:bg-slate-800">Terapkan Filter</button>
                    <a href="{{ route('incidents.index') }}" class="inline-flex items-center rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Reset</a>
                </div>
            </form>
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
