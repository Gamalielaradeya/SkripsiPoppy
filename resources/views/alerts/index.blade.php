@extends('layouts.app')

@section('title', 'Alerts')
@section('description', 'Peringatan kontekstual dengan target, evidence, impact, dan recommended action.')

@section('content')
    <x-filter-panel description="Filter visual untuk severity, target, status, dan waktu deteksi. Belum ada logic filter kompleks pada milestone ini.">
        <div class="grid gap-3 md:grid-cols-5">
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Severity</span>
                <select disabled class="mt-1 w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-400">
                    <option>All severity</option>
                </select>
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Target</span>
                <input disabled class="mt-1 w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-400" placeholder="Device/server">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Status</span>
                <select disabled class="mt-1 w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-400">
                    <option>Open / acknowledged / resolved</option>
                </select>
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Keyword</span>
                <input disabled class="mt-1 w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-400" placeholder="Evidence or rule">
            </label>
            <div class="flex items-end">
                <x-action-button disabled class="w-full">Apply Filter</x-action-button>
            </div>
        </div>
    </x-filter-panel>

    @if ($alerts->isEmpty())
        <x-empty-state
            title="Belum ada alert aktif."
            message="Alert engine belum diimplementasikan. Saat nanti dibuat, alert wajib punya target, evidence, impact, dan recommended action."
        />
    @else
        <div class="space-y-4">
            @foreach ($alerts as $alert)
                <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <x-severity-badge :severity="$alert->severity" />
                                <x-status-badge :status="$alert->status" />
                                <span class="text-xs text-slate-500">{{ $alert->detected_at?->diffForHumans() ?? '-' }}</span>
                            </div>
                            <a href="{{ route('alerts.show', $alert) }}" class="mt-3 block text-base font-semibold text-slate-950 hover:text-sky-700">{{ $alert->title }}</a>
                            <div class="mt-1 text-xs text-slate-500">{{ $alert->alert_code }} / {{ $alert->category }}</div>
                        </div>
                        <x-action-button :href="route('alerts.show', $alert)" variant="secondary">Detail</x-action-button>
                    </div>

                    <div class="mt-5 grid gap-4 text-sm md:grid-cols-2 xl:grid-cols-4">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Target</div>
                            <div class="mt-1 font-medium text-slate-900">{{ $alert->target_name ?? '-' }}</div>
                            <div class="text-xs text-slate-500">{{ $alert->target_type ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Evidence</div>
                            <div class="mt-1 text-slate-700">{{ $alert->evidence_summary ?: ($alert->evidences->count().' evidence record(s)') }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Impact</div>
                            <div class="mt-1 text-slate-700">{{ $alert->impact ?: '-' }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Recommended Action</div>
                            <div class="mt-1 text-slate-700">{{ $alert->recommended_action ?: '-' }}</div>
                        </div>
                    </div>
                </article>
            @endforeach

            <div>
                {{ $alerts->links() }}
            </div>
        </div>
    @endif
@endsection
