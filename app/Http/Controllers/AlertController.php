<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\View\View;

class AlertController extends Controller
{
    public function index(): View
    {
        return view('alerts.index', [
            'alerts' => Alert::query()->latest('detected_at')->paginate(15),
        ]);
    }

    public function show(string $id): View
    {
        return view('alerts.show', [
            'alert' => Alert::query()->with('evidences')->find($id),
            'id' => $id,
        ]);
    }
}
