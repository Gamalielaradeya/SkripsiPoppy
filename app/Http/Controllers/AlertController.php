<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AlertController extends Controller
{
    public function index(): View
    {
        return view('alerts.index', [
            'alerts' => Alert::query()
                ->with(['evidences', 'latestNotification'])
                ->latest('detected_at')
                ->paginate(15),
        ]);
    }

    public function show(string $id): View
    {
        $alert = Alert::query()
            ->with([
                'device',
                'log',
                'accurateAuditEvent',
                'evidences',
                'notifications' => fn ($query) => $query->latest(),
                'acknowledgedBy',
                'resolvedBy',
            ])
            ->find($id);

        return view('alerts.show', [
            'alert' => $alert,
            'id' => $id,
        ]);
    }

    public function acknowledge(Alert $alert): RedirectResponse
    {
        if ($alert->status === 'open') {
            $alert->forceFill([
                'status' => 'acknowledged',
                'acknowledged_by' => Auth::id(),
                'acknowledged_at' => now(),
            ])->save();
        }

        return redirect()->route('alerts.show', $alert);
    }

    public function resolve(Alert $alert): RedirectResponse
    {
        if (in_array($alert->status, ['open', 'acknowledged'], true)) {
            $alert->forceFill([
                'status' => 'resolved',
                'resolved_by' => Auth::id(),
                'resolved_at' => now(),
            ])->save();
        }

        return redirect()->route('alerts.show', $alert);
    }
}
