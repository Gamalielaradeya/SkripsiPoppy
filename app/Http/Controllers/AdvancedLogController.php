<?php

namespace App\Http\Controllers;

use App\Models\LogEntry;
use Illuminate\View\View;

class AdvancedLogController extends Controller
{
    public function index(): View
    {
        return view('advanced-logs.index', [
            'logs' => LogEntry::query()->latest('logged_at')->paginate(20),
        ]);
    }

    public function show(string $id): View
    {
        return view('advanced-logs.show', [
            'log' => LogEntry::query()->find($id),
            'id' => $id,
        ]);
    }
}
