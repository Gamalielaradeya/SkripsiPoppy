@extends('layouts.app')

@section('title', 'Alerts')
@section('description', 'Peringatan kontekstual dengan target, evidence, impact, dan recommended action.')

@section('content')
    <x-filter-panel description="Filter alert berdasarkan severity, status, dan keyword.">
            <form method="GET" action="{{ route('alerts.index') }}" class="grid gap-3 md:grid-cols-5">
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Severity</span>
                    <select name="severity" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                        <option value="">All severity</option>
                        <option value="info" @selected(request('severity') === 'info')>Info</option>
                        <option value="warning" @selected(request('severity') === 'warning')>Warning</option>
                        <option value="error" @selected(request('severity') === 'error')>Error</option>
                        <option value="critical" @selected(request('severity') === 'critical')>Critical</option>
                    </select>
                </label>
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Status</span>
                    <select name="status" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                        <option value="">All</option>
                        <option value="open" @selected(request('status') === 'open')>Open</option>
                        <option value="acknowledged" @selected(request('status') === 'acknowledged')>Acknowledged</option>
                        <option value="resolved" @selected(request('status') === 'resolved')>Resolved</option>
                    </select>
                </label>
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Target</span>
                    <input name="target" value="{{ request('target') }}" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" placeholder="Device/server">
                </label>
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Keyword</span>
                    <input name="keyword" value="{{ request('keyword') }}" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" placeholder="Evidence or rule">
                </label>
                <div class="flex items-end gap-2">
                    <x-action-button class="w-full">Apply Filter</x-action-button>
                    <a href="{{ route('alerts.index') }}" class="inline-flex items-center rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Reset</a>
                </div>
            </form>
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
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Notification</div>
                            <div class="mt-1">
                                @if ($alert->latestNotification)
                                    <x-status-badge :status="$alert->latestNotification->status" />
                                    <div class="mt-1 text-xs text-slate-500">{{ $alert->latestNotification->channel }}</div>
                                @else
                                    <span class="text-slate-500">No notification record</span>
                                @endif
                            </div>
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
