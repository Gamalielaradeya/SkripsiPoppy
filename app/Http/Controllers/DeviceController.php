<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\View\View;

class DeviceController extends Controller
{
    public function index(): View
    {
        return view('devices.index', [
            'devices' => Device::query()
                ->orderByRaw('COALESCE(last_seen_at, registered_at, created_at) DESC')
                ->paginate(15),
        ]);
    }

    public function show(string $id): View
    {
        return view('devices.show', [
            'device' => Device::query()->find($id),
            'id' => $id,
        ]);
    }
}
