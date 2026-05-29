<?php

namespace App\Http\Controllers;

use App\Models\AccurateAuditEvent;
use Illuminate\View\View;

class AccurateAuditController extends Controller
{
    public function index(): View
    {
        return view('accurate-audit.index', [
            'auditEvents' => AccurateAuditEvent::query()->latest('activity_time')->paginate(15),
        ]);
    }

    public function show(string $id): View
    {
        return view('accurate-audit.show', [
            'auditEvent' => AccurateAuditEvent::query()->find($id),
            'id' => $id,
        ]);
    }
}
