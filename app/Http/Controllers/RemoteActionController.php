<?php

namespace App\Http\Controllers;

use App\Models\RemoteAction;
use Illuminate\View\View;

class RemoteActionController extends Controller
{
    public function index(): View
    {
        return view('remote-actions.index', [
            'remoteActions' => RemoteAction::query()->with('device')->latest('requested_at')->paginate(15),
        ]);
    }

    public function show(string $id): View
    {
        return view('remote-actions.show', [
            'remoteAction' => RemoteAction::query()->with(['device', 'requester'])->find($id),
            'id' => $id,
        ]);
    }
}
