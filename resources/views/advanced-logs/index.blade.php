@extends('layouts.app')

@section('title', 'Advanced Logs')
@section('description', 'Halaman teknis/forensik untuk raw dan parsed logs dari RSyslog.')

@section('content')
    <div class="rounded-lg border border-slate-200 bg-slate-900 p-4 text-sm text-slate-200 shadow-sm">
        Advanced Logs adalah ruang investigasi teknis. Raw message, source file, dan hash tidak ditonjolkan di dashboard utama.
    </div>

    <x-filter-panel description="Filter teknis berdasarkan kolom log yang sudah tersimpan dari RSyslog parser.">
        <form method="GET" action="{{ route('advanced-logs.index') }}" class="grid gap-3 md:grid-cols-6">
            <label class="block">
                <span class="text-xs font-medium text-slate-600">From</span>
                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">To</span>
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Hostname</span>
                <input name="hostname" value="{{ $filters['hostname'] ?? '' }}" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" placeholder="Host">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Event Type</span>
                <select name="event_type" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                    <option value="">All events</option>
                    @foreach ($eventTypes as $eventType)
                        <option value="{{ $eventType }}" @selected(($filters['event_type'] ?? '') === $eventType)>{{ $eventType }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Severity</span>
                <select name="severity" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                    <option value="">All severity</option>
                    @foreach ($severities as $severity)
                        <option value="{{ $severity }}" @selected(($filters['severity'] ?? '') === $severity)>{{ $severity }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Keyword</span>
                <input name="keyword" value="{{ $filters['keyword'] ?? '' }}" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" placeholder="Raw/parsed">
            </label>
            <div class="flex items-end gap-2 md:col-span-6">
                <x-action-button class="w-full md:w-auto">Apply</x-action-button>
                <a href="{{ route('advanced-logs.index') }}" class="inline-flex items-center rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Reset</a>
            </div>
        </form>
    </x-filter-panel>

    @if ($logs->isEmpty())
        <x-empty-state
            title="Belum ada log teknis."
            message="Advanced Logs akan berisi raw log setelah RSyslog parser diimplementasikan. Dashboard utama tetap tidak menjadi raw log viewer."
        />
    @else
        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Logged At</th>
                            <th class="px-4 py-3">Hostname</th>
                            <th class="px-4 py-3">Source / Tag</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3">Severity</th>
                            <th class="px-4 py-3">Parsed Message</th>
                            <th class="px-4 py-3">Raw Preview</th>
                            <th class="px-4 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach ($logs as $log)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">{{ $log->logged_at?->format('Y-m-d H:i:s') ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $log->hostname ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $log->source ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $log->category ?? '-' }}</td>
                                <td class="px-4 py-3"><x-severity-badge :severity="$log->severity" /></td>
                                <td class="px-4 py-3">{{ \Illuminate\Support\Str::limit($log->parsed_message ?? '-', 80) }}</td>
                                <td class="px-4 py-3 font-mono text-xs">{{ \Illuminate\Support\Str::limit($log->raw_message ?? '-', 80) }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('advanced-logs.show', $log) }}" class="text-sm font-semibold text-slate-700 hover:text-slate-950">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-4 py-3">
                {{ $logs->links() }}
            </div>
        </section>
    @endif
@endsection
