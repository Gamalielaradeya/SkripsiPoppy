@extends('layouts.app')

@section('title', 'Devices')
@section('description', 'Daftar laptop Windows pengguna Accurate 5 yang terhubung ke sistem monitoring.')

@section('content')
    @if ($devices->isEmpty())
        <x-empty-state
            title="Belum ada device terdaftar."
            message="Device akan muncul setelah Windows Agent mengirim heartbeat dan parser menyimpan data berdasarkan agent_id."
        />
    @else
        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Device</th>
                            <th class="px-4 py-3">Agent ID</th>
                            <th class="px-4 py-3">Windows User</th>
                            <th class="px-4 py-3">ZeroTier IP</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Last Seen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach ($devices as $device)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <a href="{{ route('devices.show', $device) }}" class="font-medium text-slate-950 hover:text-sky-700">
                                        {{ $device->display_name }}
                                    </a>
                                    <div class="text-xs text-slate-500">{{ $device->hostname }}</div>
                                </td>
                                <td class="px-4 py-3 font-mono text-xs">{{ $device->agent_id }}</td>
                                <td class="px-4 py-3">{{ $device->windows_user ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $device->ip_zerotier ?? '-' }}</td>
                                <td class="px-4 py-3"><x-status-badge :status="$device->status" /></td>
                                <td class="px-4 py-3">{{ $device->last_seen_at?->diffForHumans() ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-4 py-3">
                {{ $devices->links() }}
            </div>
        </section>
    @endif
@endsection
