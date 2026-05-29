@extends('layouts.app')

@section('title', 'Advanced Logs')
@section('description', 'Halaman teknis/forensik untuk raw dan parsed logs dari RSyslog.')

@section('content')
    <div class="rounded-lg border border-slate-200 bg-slate-900 p-4 text-sm text-slate-200 shadow-sm">
        Advanced Logs adalah ruang investigasi teknis. Raw message, source file, dan hash tidak ditonjolkan di dashboard utama.
    </div>

    <x-filter-panel description="Filter visual untuk investigasi log. Parser/filter kompleks belum diimplementasikan pada milestone UI ini.">
        <div class="grid gap-3 md:grid-cols-6">
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Date</span>
                <input disabled class="mt-1 w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-400" placeholder="Date range">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Hostname</span>
                <input disabled class="mt-1 w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-400" placeholder="Host">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Severity</span>
                <select disabled class="mt-1 w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-400">
                    <option>All severity</option>
                </select>
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Category</span>
                <input disabled class="mt-1 w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-400" placeholder="Category">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Keyword</span>
                <input disabled class="mt-1 w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-400" placeholder="Raw/parsed">
            </label>
            <div class="flex items-end">
                <x-action-button disabled class="w-full">Apply</x-action-button>
            </div>
        </div>
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
