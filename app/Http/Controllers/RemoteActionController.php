<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class RemoteActionController extends Controller
{
    public function index(): View
    {
        return view('remote-actions.index');
    }

    public function show(string $id): View
    {
        return view('remote-actions.show', ['id' => $id]);
    }
}
