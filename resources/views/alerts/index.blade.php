@extends('layouts.app')

@section('title', 'Alerts')
@section('description', 'Peringatan kontekstual dengan target, evidence, impact, dan recommended action.')

@section('content')
    @if ($alerts->isEmpty())
        <x-empty-state
            title="Belum ada alert aktif."
            message="Alert engine belum diimplementasikan. Saat nanti dibuat, alert wajib punya target, evidence, impact, dan recommended action."
        />
    @else
        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Detected</th>
                            <th class="px-4 py-3">Severity</th>
                            <th class="px-4 py-3">Alert</th>
                            <th class="px-4 py-3">Target</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach ($alerts as $alert)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">{{ $alert->detected_at?->diffForHumans() ?? '-' }}</td>
                                <td class="px-4 py-3"><x-severity-badge :severity="$alert->severity" /></td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('alerts.show', $alert) }}" class="font-medium text-slate-950 hover:text-sky-700">{{ $alert->title }}</a>
                                    <div class="text-xs text-slate-500">{{ $alert->alert_code }} · {{ $alert->category }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $alert->target_name }}</td>
                                <td class="px-4 py-3"><x-status-badge :status="$alert->status" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-4 py-3">
                {{ $alerts->links() }}
            </div>
        </section>
    @endif
@endsection
