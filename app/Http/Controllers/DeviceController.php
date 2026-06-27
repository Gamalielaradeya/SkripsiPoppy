<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeviceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Device::query()
            ->with(['latestTelemetry', 'latestNetworkCheck', 'latestAccurateProcessSnapshot']);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('device_label', 'like', "%{$search}%")
                  ->orWhere('hostname', 'like', "%{$search}%")
                  ->orWhere('windows_user', 'like', "%{$search}%")
                  ->orWhere('ip_zerotier', 'like', "%{$search}%")
                  ->orWhere('ip_local', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($firebird = $request->query('firebird')) {
            $query->where('firebird_connection_status', $firebird);
        }

        if ($accurate = $request->query('accurate')) {
            $query->where('accurate_status', $accurate);
        }

        return view('devices.index', [
            'devices' => $query
                ->orderByRaw('COALESCE(last_seen_at, registered_at, created_at) DESC')
                ->paginate(15)
                ->appends($request->query()),
        ]);
    }

    public function show(string $id): View
    {
        $device = Device::query()->find($id);

        return view('devices.show', [
            'device' => $device,
            'id' => $id,
            'latestTelemetry' => $device?->latestTelemetry()->first(),
            'latestNetworkCheck' => $device?->latestNetworkCheck()->first(),
            'latestAccurateProcess' => $device?->latestAccurateProcessSnapshot()->first(),
            'deviceAlerts' => $device?->alerts()->latest('detected_at')->limit(5)->get() ?? collect(),
            'deviceIncidents' => $device?->incidents()->latest('detected_at')->limit(5)->get() ?? collect(),
            'deviceRemoteActions' => $device?->remoteActions()->with('requester')->latest('requested_at')->limit(5)->get() ?? collect(),
        ]);
    }
}