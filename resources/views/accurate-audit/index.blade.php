@extends('layouts.app')

@section('title', 'Accurate Audit')
@section('description', 'Audit trail Accurate dari Firebird AUDIT + USERS secara read-only.')

@section('content')
    @if ($auditEvents->isEmpty())
        <x-empty-state
            title="Belum ada data audit Accurate."
            message="Accurate Audit Reader belum diimplementasikan. Halaman ini hanya akan memakai Firebird AUDIT + USERS, bukan LOGIN."
        />
    @else
        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Activity Time</th>
                            <th class="px-4 py-3">Accurate User</th>
                            <th class="px-4 py-3">Source</th>
                            <th class="px-4 py-3">Transaction</th>
                            <th class="px-4 py-3">Invoice</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach ($auditEvents as $auditEvent)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">{{ $auditEvent->activity_time?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('accurate-audit.show', $auditEvent) }}" class="font-medium text-slate-950 hover:text-sky-700">{{ $auditEvent->accurate_username ?? '-' }}</a>
                                    <div class="text-xs text-slate-500">{{ $auditEvent->accurate_fullname ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $auditEvent->source ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $auditEvent->transaction_type ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $auditEvent->invoice_no ?? '-' }}</td>
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
