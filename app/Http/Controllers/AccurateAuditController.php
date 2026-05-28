<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AccurateAuditController extends Controller
{
    public function index(): View
    {
        return view('accurate-audit.index');
    }

    public function show(string $id): View
    {
        return view('accurate-audit.show', ['id' => $id]);
    }
}
