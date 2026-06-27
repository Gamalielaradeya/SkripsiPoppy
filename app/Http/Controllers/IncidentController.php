<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class IncidentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Incident::query()->with('alerts');

        if ($severity = $request->query('severity')) {
            $query->where('severity', $severity);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($target = $request->query('target')) {
            $query->where('target_name', 'like', "%{$target}%");
        }

        return view('incidents.index', [
            'incidents' => $query->latest('detected_at')->paginate(15)->appends($request->query()),
        ]);
    }

    public function show(string $id): View
    {
        $incident = Incident::query()
            ->with(['device', 'alerts'])
            ->find($id);

        return view('incidents.show', [
            'incident' => $incident,
            'id' => $id,
        ]);
    }

    public function acknowledge(Incident $incident): RedirectResponse
    {
        if ($incident->status === 'open') {
            $incident->forceFill([
                'status' => 'acknowledged',
                'acknowledged_at' => now(),
            ])->save();
        }

        return redirect()->route('incidents.show', $incident);
    }

    public function resolve(Incident $incident): RedirectResponse
    {
        if (in_array($incident->status, ['open', 'acknowledged'], true)) {
            $incident->forceFill([
                'status' => 'resolved',
                'resolved_at' => now(),
            ])->save();
        }

        return redirect()->route('incidents.show', $incident);
    }
}