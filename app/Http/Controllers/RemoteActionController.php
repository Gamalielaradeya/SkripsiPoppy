<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\RemoteAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RemoteActionController extends Controller
{
    public function index(): View
    {
        return view('remote-actions.index', [
            'remoteActions' => RemoteAction::query()->with(['device', 'requester'])->latest('requested_at')->paginate(15),
        ]);
    }

    public function show(string $id): View
    {
        return view('remote-actions.show', [
            'remoteAction' => RemoteAction::query()->with(['device', 'requester', 'confirmer'])->find($id),
            'id' => $id,
        ]);
    }

    public function storeRdp(Request $request, Device $device): RedirectResponse
    {
        $targetIp = $this->rdpTargetIp($device);

        if (! $targetIp) {
            return back()->with('status', 'Remote Desktop unavailable: device has no ZeroTier or local IP.');
        }

        $remoteAction = RemoteAction::query()->create([
            'device_id' => $device->id,
            'requested_by' => $request->user()->id,
            'action_type' => RemoteAction::ACTION_OPEN_RDP,
            'status' => RemoteAction::STATUS_SUCCEEDED,
            'reason' => 'Admin opened Remote Desktop launcher.',
            'requires_confirmation' => false,
            'payload' => [
                'target_ip' => $targetIp,
                'target_source' => $device->ip_zerotier ? 'ip_zerotier' : 'ip_local',
                'mstsc_command' => "mstsc /v:{$targetIp}",
            ],
            'result_message' => 'Remote Desktop launcher prepared. No credentials included.',
            'requested_at' => now(),
            'completed_at' => now(),
        ]);

        return redirect()->route('remote-actions.show', $remoteAction);
    }

    public function storeRestart(Request $request, Device $device): RedirectResponse
    {
        if (! config('monitoring.remote_action.restart_enabled', false)) {
            return back()->with('status', 'Remote restart dimatikan. Aktifkan melalui menu Pengaturan.');
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:1000'],
            'confirm_restart' => ['accepted'],
        ]);

        $expiryMinutes = max(1, (int) config('monitoring.remote_action.command_expiry_minutes', 10));
        $restartDelaySeconds = max(0, (int) config('monitoring.remote_action.restart_delay_seconds', 30));

        $remoteAction = RemoteAction::query()->create([
            'device_id' => $device->id,
            'requested_by' => $request->user()->id,
            'action_type' => RemoteAction::ACTION_RESTART_CLIENT,
            'status' => RemoteAction::STATUS_PENDING,
            'reason' => $validated['reason'],
            'requires_confirmation' => true,
            'confirmed_by' => $request->user()->id,
            'confirmed_at' => now(),
            'payload' => [
                'restart_delay_seconds' => $restartDelaySeconds,
            ],
            'requested_at' => now(),
            'expires_at' => now()->addMinutes($expiryMinutes),
        ]);

        return redirect()->route('remote-actions.show', $remoteAction);
    }

    public function downloadRdpFile(RemoteAction $remoteAction): Response
    {
        abort_unless($remoteAction->action_type === RemoteAction::ACTION_OPEN_RDP, 404);

        $targetIp = $remoteAction->payload['target_ip'] ?? null;
        abort_if(blank($targetIp), 404);

        $safeName = Str::slug($remoteAction->device?->display_name ?: 'device') ?: 'device';
        $contents = implode("\r\n", [
            'screen mode id:i:2',
            'use multimon:i:0',
            'desktopwidth:i:1280',
            'desktopheight:i:720',
            'session bpp:i:32',
            "full address:s:{$targetIp}",
            'prompt for credentials:i:1',
            'authentication level:i:2',
            'enablecredsspsupport:i:1',
            '',
        ]);

        return response($contents, 200, [
            'Content-Type' => 'application/x-rdp',
            'Content-Disposition' => 'attachment; filename="'.$safeName.'-'.$remoteAction->id.'.rdp"',
        ]);
    }

    private function rdpTargetIp(Device $device): ?string
    {
        return $device->ip_zerotier ?: $device->ip_local;
    }
}
