<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\View\View;

class DeviceController extends Controller
{
    public function index(): View
    {
        return view('devices.index', [
            'devices' => Device::query()
                ->with('latestTelemetry')
                ->orderByRaw('COALESCE(last_seen_at, registered_at, created_at) DESC')
                ->paginate(15),
        ]);
    }

    public function show(string $id): View
    {
        $device = Device::query()->find($id);

        return view('devices.show', [
            'device' => $device,
            'id' => $id,
            'latestTelemetry' => $device?->latestTelemetry()->first(),
            'latestNetworkCheck' => $device?->networkChecks()->latest('checked_at')->first(),
            'latestAccurateProcess' => $device?->accurateProcessSnapshots()->latest('checked_at')->first(),
            'deviceAlerts' => $device?->alerts()->latest('detected_at')->limit(5)->get() ?? collect(),
            'deviceIncidents' => $device?->incidents()->latest('detected_at')->limit(5)->get() ?? collect(),
            'deviceRemoteActions' => $device?->remoteActions()->latest('requested_at')->limit(5)->get() ?? collect(),
        ]);
    }
}
