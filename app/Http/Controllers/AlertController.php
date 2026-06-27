<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AlertController extends Controller
{
    public function index(Request $request): View
    {
        $query = Alert::query()
            ->with(['evidences', 'latestNotification']);

        if ($severity = $request->query('severity')) {
            $query->where('severity', $severity);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($target = $request->query('target')) {
            $query->where('target_name', 'like', "%{$target}%");
        }

        if ($keyword = $request->query('keyword')) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('evidence_summary', 'like', "%{$keyword}%")
                  ->orWhere('target_name', 'like', "%{$keyword}%");
            });
        }

        return view('alerts.index', [
            'alerts' => $query
                ->latest('detected_at')
                ->paginate(15)
                ->appends($request->query()),
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
