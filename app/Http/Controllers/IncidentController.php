<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\View\View;

class IncidentController extends Controller
{
    public function index(): View
    {
        return view('incidents.index', [
            'incidents' => Incident::query()->with('alerts')->latest('detected_at')->paginate(15),
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
