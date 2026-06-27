<?php

namespace App\Http\Controllers;

use App\Models\AccurateProcessSnapshot;
use App\Models\AgentCredential;
use App\Models\Device;
use App\Models\DeviceTelemetry;
use App\Models\NetworkCheck;
use App\Models\RemoteAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AgentApiController extends Controller
{
    private function gateCheck(): ?JsonResponse
    {
        if (! config('monitoring.remote_action.agent_api_enabled', false)) {
            return response()->json([
                'message' => 'Agent API is disabled. Enable it via Settings.',
            ], 503);
        }

        return null;
    }

    public function register(Request $request): JsonResponse
    {
        if ($result = $this->gateCheck()) {
            return $result;
        }
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
        if ($result = $this->gateCheck()) {
            return $result;
        }
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
            'cpu_usage_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'ram_usage_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'disk_usage_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'uptime_seconds' => ['nullable', 'integer', 'min:0'],
            'last_boot_at' => ['nullable', 'date'],
            'firebird_check' => ['nullable', 'array'],
            'firebird_check.target_type' => ['nullable', Rule::in(['firebird', 'vps', 'dashboard', 'other'])],
            'firebird_check.target_name' => ['nullable', 'string', 'max:150'],
            'firebird_check.target_host' => ['nullable', 'string', 'max:255'],
            'firebird_check.target_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'firebird_check.ping_status' => ['nullable', Rule::in(['ok', 'timeout', 'failed', 'unknown'])],
            'firebird_check.ping_latency_ms' => ['nullable', 'numeric', 'min:0'],
            'firebird_check.tcp_status' => ['nullable', Rule::in(['connected', 'timeout', 'refused', 'failed', 'unknown'])],
            'firebird_check.tcp_latency_ms' => ['nullable', 'numeric', 'min:0'],
            'firebird_check.latency_ms' => ['nullable', 'numeric', 'min:0'],
            'firebird_check.packet_loss_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'firebird_check.status' => ['nullable', Rule::in(['normal', 'warning', 'error', 'critical', 'unknown'])],
            'firebird_check.failure_reason' => ['nullable', 'string', 'max:500'],
            'firebird_check.checked_at' => ['nullable', 'date'],
            'accurate_process' => ['nullable', 'array'],
            'accurate_process.process_name' => ['nullable', 'string', 'max:100'],
            'accurate_process.process_status' => ['nullable', Rule::in(['running', 'not_running', 'unknown'])],
            'accurate_process.process_pid' => ['nullable', 'integer', 'min:1'],
            'accurate_process.process_owner' => ['nullable', 'string', 'max:150'],
            'accurate_process.process_path' => ['nullable', 'string', 'max:500'],
            'accurate_process.process_started_at' => ['nullable', 'date'],
            'accurate_process.checked_at' => ['nullable', 'date'],
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
            $this->storeFirebirdCheck($device, $validated);
            $this->storeAccurateProcessSnapshot($device, $validated);
        });

        return response()->json([
            'status' => 'heartbeat_received',
            'device' => $this->deviceResponse($device->refresh()),
        ]);
    }

    public function pendingCommands(Request $request): JsonResponse
    {
        if ($result = $this->gateCheck()) {
            return $result;
        }
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

        /** @var Device $device */
        $device = $auth['device'];

        $commands = DB::transaction(function () use ($device) {
            RemoteAction::query()
                ->where('device_id', $device->id)
                ->where('status', RemoteAction::STATUS_PENDING)
                ->whereNotNull('expires_at')
                ->where('expires_at', '<=', now())
                ->update(['status' => RemoteAction::STATUS_EXPIRED]);

            $actions = RemoteAction::query()
                ->where('device_id', $device->id)
                ->where('action_type', RemoteAction::ACTION_RESTART_CLIENT)
                ->where('status', RemoteAction::STATUS_PENDING)
                ->where(function ($query): void {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->orderBy('requested_at')
                ->lockForUpdate()
                ->get();

            $actions->each(function (RemoteAction $action): void {
                $action->forceFill([
                    'status' => RemoteAction::STATUS_PICKED_UP,
                    'picked_up_at' => now(),
                ])->save();
            });

            return $actions->map(fn (RemoteAction $action): array => [
                'id' => $action->id,
                'action_type' => $action->action_type,
                'status' => $action->status,
                'reason' => $action->reason,
                'payload' => $action->payload ?? [],
                'requested_at' => $action->requested_at?->toIso8601String(),
                'expires_at' => $action->expires_at?->toIso8601String(),
            ])->values();
        });

        return response()->json([
            'status' => 'ok',
            'commands' => $commands,
        ]);
    }

    public function commandResult(Request $request, RemoteAction $remoteAction): JsonResponse
    {
        if ($result = $this->gateCheck()) {
            return $result;
        }
        $validated = $request->validate([
            'agent_id' => ['required', 'string', 'max:64'],
            'status' => ['required', Rule::in([RemoteAction::STATUS_SUCCEEDED, RemoteAction::STATUS_FAILED])],
            'result_message' => ['nullable', 'string', 'max:1000'],
            'error_message' => ['nullable', 'string', 'max:1000'],
            'executed_at' => ['nullable', 'date'],
        ]);

        $auth = $this->authenticateAgent($request, $validated['agent_id']);

        if (! $auth['ok']) {
            return response()->json([
                'message' => $auth['message'],
            ], 401);
        }

        /** @var Device $device */
        $device = $auth['device'];

        if ((int) $remoteAction->device_id !== (int) $device->id) {
            return response()->json([
                'message' => 'Command does not belong to this agent.',
            ], 403);
        }

        if ($remoteAction->action_type !== RemoteAction::ACTION_RESTART_CLIENT) {
            return response()->json([
                'message' => 'Unsupported command type.',
            ], 422);
        }

        /** @var AgentCredential $credential */
        $credential = $auth['credential'];
        $credential->forceFill([
            'last_used_at' => now(),
        ])->save();

        $remoteAction->forceFill([
            'status' => $validated['status'],
            'executed_at' => $validated['executed_at'] ?? now(),
            'completed_at' => now(),
            'result_message' => $validated['result_message'] ?? null,
            'error_message' => $validated['error_message'] ?? null,
        ])->save();

        return response()->json([
            'status' => 'result_recorded',
            'command' => [
                'id' => $remoteAction->id,
                'action_type' => $remoteAction->action_type,
                'status' => $remoteAction->status,
                'completed_at' => $remoteAction->completed_at?->toIso8601String(),
            ],
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
        $telemetryKeys = [
            'cpu_usage_percent',
            'ram_usage_percent',
            'disk_usage_percent',
            'uptime_seconds',
            'last_boot_at',
        ];

        if (! collect($telemetryKeys)->contains(fn (string $key): bool => array_key_exists($key, $payload))) {
            return;
        }

        DeviceTelemetry::query()->create([
            'device_id' => $device->id,
            'agent_id' => $device->agent_id,
            'hostname' => $payload['hostname'] ?? $device->hostname,
            'windows_user' => $payload['windows_user'] ?? $device->windows_user,
            'ip_zerotier' => $payload['zerotier_ip'] ?? $payload['ip_zerotier'] ?? $device->ip_zerotier,
            'ip_local' => $payload['ip_local'] ?? $payload['ip_address'] ?? $device->ip_local,
            'cpu_usage_percent' => $payload['cpu_usage_percent'] ?? null,
            'ram_usage_percent' => $payload['ram_usage_percent'] ?? null,
            'disk_usage_percent' => $payload['disk_usage_percent'] ?? null,
            'uptime_seconds' => $payload['uptime_seconds'] ?? null,
            'last_boot_at' => $payload['last_boot_at'] ?? null,
            'agent_status' => 'online',
            'raw_payload' => $payload,
            'reported_at' => now(),
        ]);
    }

    private function storeFirebirdCheck(Device $device, array $payload): void
    {
        if (! array_key_exists('firebird_check', $payload) || ! is_array($payload['firebird_check'])) {
            return;
        }

        $check = $payload['firebird_check'];
        if ($this->hasNoSubmittedValues($check)) {
            return;
        }

        $tcpStatus = $check['tcp_status'] ?? 'unknown';
        $summaryStatus = $this->firebirdSummaryStatus($tcpStatus);
        $latency = $check['tcp_latency_ms'] ?? $check['latency_ms'] ?? null;

        NetworkCheck::query()->create([
            'device_id' => $device->id,
            'target_type' => $check['target_type'] ?? 'firebird',
            'target_name' => $check['target_name'] ?? null,
            'target_host' => $check['target_host'] ?? null,
            'target_port' => $check['target_port'] ?? null,
            'ping_status' => $check['ping_status'] ?? 'unknown',
            'ping_latency_ms' => $check['ping_latency_ms'] ?? null,
            'tcp_status' => $tcpStatus,
            'tcp_latency_ms' => $latency,
            'packet_loss_percent' => $check['packet_loss_percent'] ?? null,
            'status' => $check['status'] ?? $this->networkOverallStatus($tcpStatus),
            'raw_payload' => $check,
            'checked_at' => $check['checked_at'] ?? now(),
        ]);

        $device->forceFill([
            'firebird_connection_status' => $summaryStatus,
        ])->save();
    }

    private function storeAccurateProcessSnapshot(Device $device, array $payload): void
    {
        if (! array_key_exists('accurate_process', $payload) || ! is_array($payload['accurate_process'])) {
            return;
        }

        $process = $payload['accurate_process'];
        if ($this->hasNoSubmittedValues($process)) {
            return;
        }

        $processStatus = $process['process_status'] ?? 'unknown';

        AccurateProcessSnapshot::query()->create([
            'device_id' => $device->id,
            'process_name' => $process['process_name'] ?? 'accurate.exe',
            'process_status' => $processStatus,
            'process_pid' => $process['process_pid'] ?? null,
            'process_owner' => $process['process_owner'] ?? null,
            'process_path' => $process['process_path'] ?? null,
            'process_started_at' => $process['process_started_at'] ?? null,
            'raw_payload' => $process,
            'checked_at' => $process['checked_at'] ?? now(),
        ]);

        $device->forceFill([
            'accurate_status' => $processStatus,
        ])->save();
    }

    private function hasNoSubmittedValues(array $payload): bool
    {
        return collect($payload)
            ->filter(fn ($value): bool => $value !== null && $value !== '')
            ->isEmpty();
    }

    private function firebirdSummaryStatus(string $tcpStatus): string
    {
        return match ($tcpStatus) {
            'connected' => 'connected',
            'timeout' => 'timeout',
            'refused', 'failed' => 'failed',
            default => 'unknown',
        };
    }

    private function networkOverallStatus(string $tcpStatus): string
    {
        return match ($tcpStatus) {
            'connected' => 'normal',
            'timeout', 'refused', 'failed' => 'error',
            default => 'unknown',
        };
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
