<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class IncidentController extends Controller
{
    public function index(): View
    {
        return view('incidents.index');
    }

    public function show(string $id): View
    {
        return view('incidents.show', ['id' => $id]);
    }
}
