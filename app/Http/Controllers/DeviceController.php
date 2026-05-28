<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DeviceController extends Controller
{
    public function index(): View
    {
        return view('devices.index');
    }

    public function show(string $id): View
    {
        return view('devices.show', ['id' => $id]);
    }
}
