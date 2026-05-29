<?php

namespace App\Http\Controllers;

use App\Models\AccurateAuditEvent;
use App\Models\Alert;
use App\Models\Device;
use App\Models\Incident;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard.index', [
            'totalDevices' => Device::query()->count(),
            'onlineDevices' => Device::query()->where('agent_status', 'online')->count(),
            'firebirdConnectedDevices' => Device::query()->where('firebird_connection_status', 'connected')->count(),
            'accurateRunningDevices' => Device::query()->where('accurate_status', 'running')->count(),
            'openAlerts' => Alert::query()->where('status', 'open')->count(),
            'auditEventsToday' => AccurateAuditEvent::query()->whereDate('activity_time', now())->count(),
            'openIncidents' => Incident::query()->where('status', 'open')->count(),
            'latestDevices' => Device::query()
                ->with('latestTelemetry')
                ->orderByRaw('COALESCE(last_seen_at, registered_at, created_at) DESC')
                ->limit(8)
                ->get(),
            'latestAuditEvents' => AccurateAuditEvent::query()
                ->latest('activity_time')
                ->limit(6)
                ->get(),
            'latestIncidents' => Incident::query()
                ->where('status', 'open')
                ->latest('detected_at')
                ->limit(5)
                ->get(),
            'latestAlerts' => Alert::query()
                ->where('status', 'open')
                ->latest('detected_at')
                ->limit(5)
                ->get(),
        ]);
    }
}
