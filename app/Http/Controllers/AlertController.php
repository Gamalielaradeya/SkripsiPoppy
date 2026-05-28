<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AlertController extends Controller
{
    public function index(): View
    {
        return view('alerts.index');
    }

    public function show(string $id): View
    {
        return view('alerts.show', ['id' => $id]);
    }
}
