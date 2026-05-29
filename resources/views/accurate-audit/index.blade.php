@extends('layouts.app')

@section('title', 'Accurate Audit')
@section('description', 'Audit trail Accurate dari Firebird AUDIT + USERS secara read-only.')

@section('content')
    <x-filter-panel description="Filter visual untuk audit Firebird. Data tetap hanya dari record accurate_audit_events yang sudah ada.">
        <div class="grid gap-3 md:grid-cols-5">
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Date Range</span>
                <input disabled class="mt-1 w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-400" placeholder="Start - End">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Accurate User</span>
                <input disabled class="mt-1 w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-400" placeholder="Username">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Source / Module</span>
                <input disabled class="mt-1 w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-400" placeholder="Module">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Transaction Type</span>
                <input disabled class="mt-1 w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-400" placeholder="Type">
            </label>
            <div class="flex items-end">
                <x-action-button disabled class="w-full">Apply Filter</x-action-button>
            </div>
        </div>
    </x-filter-panel>

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
                            <th class="px-4 py-3">Audit Time</th>
                            <th class="px-4 py-3">Accurate User</th>
                            <th class="px-4 py-3">Full Name</th>
                            <th class="px-4 py-3">Source / Module</th>
                            <th class="px-4 py-3">Transaction Type</th>
                            <th class="px-4 py-3">Description</th>
                            <th class="px-4 py-3">Reference / Invoice</th>
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
