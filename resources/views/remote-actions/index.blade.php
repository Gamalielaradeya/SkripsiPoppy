@extends('layouts.app')

@section('title', 'Remote Actions')
@section('description', 'Riwayat tindakan remote manual oleh administrator.')

@section('content')
    @if ($remoteActions->isEmpty())
        <x-empty-state
            title="Belum ada tindakan remote."
            message="Remote action execution belum diimplementasikan. Restart nanti wajib manual, confirmed, reasoned, dan audited."
        />
    @else
        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Requested</th>
                            <th class="px-4 py-3">Action</th>
                            <th class="px-4 py-3">Device</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach ($remoteActions as $remoteAction)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">{{ $remoteAction->requested_at?->diffForHumans() ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('remote-actions.show', $remoteAction) }}" class="font-medium text-slate-950 hover:text-sky-700">{{ $remoteAction->action_type }}</a>
                                </td>
                                <td class="px-4 py-3">{{ $remoteAction->device?->display_name ?? '-' }}</td>
                                <td class="px-4 py-3"><x-status-badge :status="$remoteAction->status" /></td>
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
