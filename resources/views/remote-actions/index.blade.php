@extends('layouts.app')

@section('title', 'Remote Actions')
@section('description', 'Riwayat tindakan remote manual oleh administrator.')

@section('content')
    <x-filter-panel description="Riwayat real dari Remote Desktop launcher dan Restart Client manual. Tidak ada data contoh.">
        <div class="grid gap-3 md:grid-cols-5">
            <div class="rounded-md border border-slate-200 p-3">
                <div class="text-xs font-medium text-slate-500">Total Records</div>
                <div class="mt-1 text-lg font-semibold text-slate-950">{{ $remoteActions->total() }}</div>
            </div>
            <div class="rounded-md border border-slate-200 p-3">
                <div class="text-xs font-medium text-slate-500">Pending</div>
                <div class="mt-1 text-lg font-semibold text-slate-950">{{ $remoteActions->getCollection()->where('status', 'pending')->count() }}</div>
            </div>
            <div class="rounded-md border border-slate-200 p-3">
                <div class="text-xs font-medium text-slate-500">Picked Up</div>
                <div class="mt-1 text-lg font-semibold text-slate-950">{{ $remoteActions->getCollection()->where('status', 'picked_up')->count() }}</div>
            </div>
            <div class="rounded-md border border-slate-200 p-3">
                <div class="text-xs font-medium text-slate-500">Succeeded</div>
                <div class="mt-1 text-lg font-semibold text-slate-950">{{ $remoteActions->getCollection()->where('status', 'succeeded')->count() }}</div>
            </div>
            <div class="rounded-md border border-slate-200 p-3">
                <div class="text-xs font-medium text-slate-500">Failed</div>
                <div class="mt-1 text-lg font-semibold text-slate-950">{{ $remoteActions->getCollection()->where('status', 'failed')->count() }}</div>
            </div>
        </div>
    </x-filter-panel>

    @if ($remoteActions->isEmpty())
        <x-empty-state
            title="Belum ada tindakan remote."
            message="Remote Desktop dan Restart Client akan muncul di sini setelah admin menjalankan action dari Device Detail."
        />
    @else
        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Action Type</th>
                            <th class="px-4 py-3">Target Device</th>
                            <th class="px-4 py-3">Admin / Requester</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Requested Time</th>
                            <th class="px-4 py-3">Executed Time</th>
                            <th class="px-4 py-3">Completed Time</th>
                            <th class="px-4 py-3">Reason</th>
                            <th class="px-4 py-3">Result</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach ($remoteActions as $remoteAction)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <a href="{{ route('remote-actions.show', $remoteAction) }}" class="font-medium text-slate-950 hover:text-sky-700">{{ $remoteAction->action_type }}</a>
                                </td>
                                <td class="px-4 py-3">{{ $remoteAction->device?->display_name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $remoteAction->requester?->name ?? '-' }}</td>
                                <td class="px-4 py-3"><x-status-badge :status="$remoteAction->status" /></td>
                                <td class="px-4 py-3">{{ $remoteAction->requested_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $remoteAction->executed_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $remoteAction->completed_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td class="px-4 py-3">{{ \Illuminate\Support\Str::limit($remoteAction->reason ?? '-', 60) }}</td>
                                <td class="px-4 py-3">{{ \Illuminate\Support\Str::limit($remoteAction->result_message ?? $remoteAction->error_message ?? '-', 60) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-4 py-3">
                {{ $remoteActions->links() }}
            </div>
        </section>
    @endif
@endsection
