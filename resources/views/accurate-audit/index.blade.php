@extends('layouts.app')

@section('title', 'Accurate Audit')
@section('description', 'Audit trail Accurate dari Firebird AUDIT + USERS secara read-only.')

@section('content')
    <x-filter-panel description="Filter data audit tersimpan dari Firebird AUDIT + USERS.">
        <form method="GET" action="{{ route('accurate-audit.index') }}" class="grid gap-3 lg:grid-cols-7">
            <label class="block">
                <span class="text-xs font-medium text-slate-600">From</span>
                <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">To</span>
                <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Accurate User</span>
                <input name="username" value="{{ $filters['username'] ?? '' }}" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" placeholder="Username">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Source / Module</span>
                <input name="source" value="{{ $filters['source'] ?? '' }}" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" placeholder="Module">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Transaction Type</span>
                <input name="transaction_type" value="{{ $filters['transaction_type'] ?? '' }}" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" placeholder="Type">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Keyword</span>
                <input name="keyword" value="{{ $filters['keyword'] ?? '' }}" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" placeholder="Description / invoice">
            </label>
            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-slate-900 px-3 py-2 text-sm font-medium text-white transition hover:bg-slate-800">Terapkan Filter</button>
                <a href="{{ route('accurate-audit.index') }}" class="inline-flex items-center rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Reset</a>
            </div>
        </form>
    </x-filter-panel>

    @if ($auditEvents->isEmpty())
        <x-empty-state
            title="Belum ada data audit Accurate."
            message="Belum ada event audit tersimpan dari Firebird AUDIT + USERS. Jalankan sync setelah koneksi read-only dikonfigurasi."
        />
    @else
        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Audit Time</th>
                            <th class="px-4 py-3">Accurate User</th>
                            <th class="px-4 py-3">Full Name</th>
                            <th class="px-4 py-3">Source / Module</th>
                            <th class="px-4 py-3">Transaction Type</th>
                            <th class="px-4 py-3">Description</th>
                            <th class="px-4 py-3">Reference / Invoice</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach ($auditEvents as $auditEvent)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">{{ $auditEvent->activity_time?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('accurate-audit.show', $auditEvent) }}" class="font-medium text-slate-950 hover:text-sky-700">{{ $auditEvent->accurate_username ?? '-' }}</a>
                                </td>
                                <td class="px-4 py-3">{{ $auditEvent->accurate_fullname ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $auditEvent->source ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $auditEvent->transaction_type ?? '-' }}</td>
                                <td class="px-4 py-3">{{ \Illuminate\Support\Str::limit($auditEvent->transaction_description ?? '-', 80) }}</td>
                                <td class="px-4 py-3">{{ $auditEvent->invoice_no ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $auditEvent->status ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-4 py-3">
                {{ $auditEvents->links() }}
            </div>
        </section>
    @endif
@endsection
