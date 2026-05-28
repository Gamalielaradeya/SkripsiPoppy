<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AdvancedLogController extends Controller
{
    public function index(): View
    {
        return view('advanced-logs.index');
    }

    public function show(string $id): View
    {
        return view('advanced-logs.show', ['id' => $id]);
    }
}
