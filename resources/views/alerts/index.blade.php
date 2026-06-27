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
                    <x-autocomplete-input name="target" :value="request('target')" placeholder="Device/server" :endpoint="route('search.alert-targets')" label="Target" />
                </label>
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Keyword</span>
                    <x-autocomplete-input name="keyword" :value="request('keyword')" placeholder="Judul, ringkasan, atau aturan" :endpoint="route('search.alert-keywords')" label="Keyword" />
                </label>
                <div class="flex items-end gap-2">
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-slate-900 px-3 py-2 text-sm font-medium text-white transition hover:bg-slate-800">Terapkan Filter</button>
                    <a href="{{ route('alerts.index') }}" class="inline-flex items-center rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Reset</a>
                </div>
            </form>

            @php $openCount = $alerts->getCollection()->where('status', 'open')->count(); $ackCount = $alerts->getCollection()->whereIn('status', ['open', 'acknowledged'])->count(); @endphp

            @if ($alerts->isNotEmpty() && ($openCount > 0 || $ackCount > 0))
                <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-3">
                    <span class="text-xs font-medium uppercase tracking-wide text-slate-400">Bulk Actions:</span>
                    @if ($openCount > 0)
                        <div x-data="{ show: false }">
                            <button type="button" x-on:click="show = true" class="inline-flex items-center justify-center rounded-md border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50">
                                Acknowledge All ({{ $openCount }})
                            </button>
                            <div x-cloak x-show="show" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 px-4">
                                <div class="w-full max-w-sm rounded-lg bg-white p-6 shadow-xl" x-on:click.outside="show = false">
                                    <h3 class="text-base font-semibold text-slate-950">Acknowledge All Alerts</h3>
                                    <p class="mt-2 text-sm text-slate-600">This will acknowledge {{ $openCount }} open alert(s). Are you sure?</p>
                                    <div class="mt-5 flex justify-end gap-3">
                                        <button type="button" x-on:click="show = false" class="rounded-md border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                                        <form method="POST" action="{{ route('alerts.acknowledge-all') }}">
                                            @csrf
                                            <button type="submit" class="rounded-md bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-800">Confirm</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if ($ackCount > 0)
                        <div x-data="{ show: false }">
                            <button type="button" x-on:click="show = true" class="inline-flex items-center justify-center rounded-md bg-slate-900 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-slate-800">
                                Resolve All ({{ $ackCount }})
                            </button>
                            <div x-cloak x-show="show" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 px-4">
                                <div class="w-full max-w-sm rounded-lg bg-white p-6 shadow-xl" x-on:click.outside="show = false">
                                    <h3 class="text-base font-semibold text-slate-950">Resolve All Alerts</h3>
                                    <p class="mt-2 text-sm text-slate-600">This will resolve {{ $ackCount }} open/acknowledged alert(s). Are you sure?</p>
                                    <div class="mt-5 flex justify-end gap-3">
                                        <button type="button" x-on:click="show = false" class="rounded-md border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                                        <form method="POST" action="{{ route('alerts.resolve-all') }}">
                                            @csrf
                                            <button type="submit" class="rounded-md bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-800">Confirm</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </x-filter-panel>

    @if (session('status'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-900">
            {{ session('status') }}
        </div>
    @endif

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
