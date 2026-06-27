<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;
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
        return view('incidents.show', [
            'incident' => Incident::query()->with('alerts')->find($id),
            'id' => $id,
        ]);
    }
}