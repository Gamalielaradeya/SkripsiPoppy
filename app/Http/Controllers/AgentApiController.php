<?php

namespace App\Http\Controllers;

use App\Models\AgentCredential;
use App\Models\Device;
use App\Models\DeviceTelemetry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AgentApiController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'agent_id' => ['required', 'string', 'max:64'],
            'hostname' => ['required', 'string', 'max:100'],
            'windows_user' => ['nullable', 'string', 'max:150'],
            'ip_address' => ['nullable', 'ip'],
            'ip_local' => ['nullable', 'ip'],
            'zerotier_ip' => ['nullable', 'ip'],
            'ip_zerotier' => ['nullable', 'ip'],
            'os_name' => ['nullable', 'string', 'max:100'],
            'os_version' => ['nullable', 'string', 'max:100'],
            'agent_version' => ['nullable', 'string', 'max:50'],
            'rdp_status' => ['nullable', 'string', 'max:30'],
        ]);

        $plainToken = null;

        $device = DB::transaction(function () use ($validated, &$plainToken): Device {
            $device = Device::query()->where('agent_id', $validated['agent_id'])->first();
            $isNewDevice = ! $device;

            $device ??= new Device(['agent_id' => $validated['agent_id']]);
            $device->fill($this->deviceMetadata($validated));

            if ($isNewDevice) {
                $device->registered_at = now();
            }

            if ($isNewDevice || blank($device->device_label)) {
                $device->device_label = $validated['hostname'];
            }

            $device->save();

            if (! $this->activeCredentialQuery($device)->exists()) {
                $plainToken = Str::random(64);

                $device->agentCredentials()->create([
                    'token_name' => 'default-agent-token',
                    'token_hash' => Hash::make($plainToken),
                ]);
            }

            return $device->refresh();
        });

        return response()->json([
            'status' => 'registered',
            'device' => $this->deviceResponse($device),
            'credential' => [
                'token_type' => 'Bearer',
                'token' => $plainToken,
                'returned_once' => $plainToken !== null,
            ],
        ], $plainToken ? 201 : 200);
    }

    public function heartbeat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'agent_id' => ['required', 'string', 'max:64'],
            'hostname' => ['nullable', 'string', 'max:100'],
            'windows_user' => ['nullable', 'string', 'max:150'],
            'ip_address' => ['nullable', 'ip'],
            'ip_local' => ['nullable', 'ip'],
            'zerotier_ip' => ['nullable', 'ip'],
            'ip_zerotier' => ['nullable', 'ip'],
            'agent_version' => ['nullable', 'string', 'max:50'],
            'rdp_status' => ['nullable', 'string', 'max:30'],
            'uptime_seconds' => ['nullable', 'integer', 'min:0'],
            'last_boot_at' => ['nullable', 'date'],
        ]);

        $auth = $this->authenticateAgent($request, $validated['agent_id']);

        if (! $auth['ok']) {
            return response()->json([
                'message' => $auth['message'],
            ], 401);
        }

        /** @var Device $device */
        $device = $auth['device'];
        /** @var AgentCredential $credential */
        $credential = $auth['credential'];

        DB::transaction(function () use ($validated, $device, $credential): void {
            $device->fill($this->deviceMetadata($validated));
            $device->last_seen_at = now();
            $device->agent_status = 'online';
            $device->status = 'online';
            $device->save();

            $credential->forceFill([
                'last_used_at' => now(),
            ])->save();

            $this->storeHeartbeatSnapshot($device, $validated);
        });

        return response()->json([
            'status' => 'heartbeat_received',
            'device' => $this->deviceResponse($device->refresh()),
        ]);
    }

    public function pendingCommands(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'agent_id' => ['required', 'string', 'max:64'],
        ]);

        $auth = $this->authenticateAgent($request, $validated['agent_id']);

        if (! $auth['ok']) {
            return response()->json([
                'message' => $auth['message'],
            ], 401);
        }

        /** @var AgentCredential $credential */
        $credential = $auth['credential'];
        $credential->forceFill([
            'last_used_at' => now(),
        ])->save();

        return response()->json([
            'status' => 'ok',
            'commands' => [],
        ]);
    }

    private function authenticateAgent(Request $request, string $agentId): array
    {
        $token = $request->bearerToken() ?: $request->header('X-Agent-Token');

        if (! $token) {
            return ['ok' => false, 'message' => 'Agent token is required.'];
        }

        $device = Device::query()->where('agent_id', $agentId)->first();

        if (! $device) {
            return ['ok' => false, 'message' => 'Agent is not registered.'];
        }

        $credential = $this->activeCredentialQuery($device)
            ->get()
            ->first(fn (AgentCredential $credential): bool => Hash::check($token, $credential->token_hash));

        if (! $credential) {
            return ['ok' => false, 'message' => 'Agent token is invalid.'];
        }

        return [
            'ok' => true,
            'device' => $device,
            'credential' => $credential,
        ];
    }

    private function activeCredentialQuery(Device $device)
    {
        return $device->agentCredentials()
            ->whereNull('revoked_at')
            ->where(function ($query): void {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    private function deviceMetadata(array $payload): array
    {
        return array_filter([
            'hostname' => $payload['hostname'] ?? null,
            'windows_user' => $payload['windows_user'] ?? null,
            'ip_zerotier' => $payload['zerotier_ip'] ?? $payload['ip_zerotier'] ?? null,
            'ip_local' => $payload['ip_local'] ?? $payload['ip_address'] ?? null,
            'os_name' => $payload['os_name'] ?? null,
            'os_version' => $payload['os_version'] ?? null,
            'agent_version' => $payload['agent_version'] ?? null,
            'rdp_status' => $payload['rdp_status'] ?? null,
        ], fn ($value): bool => $value !== null);
    }

    private function storeHeartbeatSnapshot(Device $device, array $payload): void
    {
        if (! array_key_exists('uptime_seconds', $payload) && ! array_key_exists('last_boot_at', $payload)) {
            return;
        }

        DeviceTelemetry::query()->create([
            'device_id' => $device->id,
            'agent_id' => $device->agent_id,
            'hostname' => $payload['hostname'] ?? $device->hostname,
            'windows_user' => $payload['windows_user'] ?? $device->windows_user,
            'ip_zerotier' => $payload['zerotier_ip'] ?? $payload['ip_zerotier'] ?? $device->ip_zerotier,
            'ip_local' => $payload['ip_local'] ?? $payload['ip_address'] ?? $device->ip_local,
            'uptime_seconds' => $payload['uptime_seconds'] ?? null,
            'last_boot_at' => $payload['last_boot_at'] ?? null,
            'agent_status' => 'online',
            'raw_payload' => $payload,
            'reported_at' => now(),
        ]);
    }

    private function deviceResponse(Device $device): array
    {
        return [
            'id' => $device->id,
            'agent_id' => $device->agent_id,
            'hostname' => $device->hostname,
            'device_label' => $device->device_label,
            'windows_user' => $device->windows_user,
            'ip_zerotier' => $device->ip_zerotier,
            'ip_local' => $device->ip_local,
            'agent_version' => $device->agent_version,
            'agent_status' => $device->agent_status,
            'rdp_status' => $device->rdp_status,
            'status' => $device->status,
            'last_seen_at' => $device->last_seen_at?->toIso8601String(),
            'registered_at' => $device->registered_at?->toIso8601String(),
        ];
    }
}
