<?php

namespace Tests\Feature;

use App\Models\AccurateProcessSnapshot;
use App\Models\AgentCredential;
use App\Models\Device;
use App\Models\DeviceTelemetry;
use App\Models\NetworkCheck;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AgentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_can_register_and_receives_token_once(): void
    {
        $response = $this->postJson('/api/agent/register', [
            'agent_id' => 'agent-api-test-001',
            'hostname' => 'HOST-001',
            'windows_user' => 'HOST-001\User',
            'zerotier_ip' => '10.147.20.11',
            'agent_version' => '1.0.0',
        ]);

        $response->assertCreated()
            ->assertJsonPath('status', 'registered')
            ->assertJsonPath('device.agent_id', 'agent-api-test-001')
            ->assertJsonPath('device.device_label', 'HOST-001')
            ->assertJsonPath('credential.token_type', 'Bearer')
            ->assertJsonPath('credential.returned_once', true)
            ->assertJsonMissingPath('credential.token_hash');

        $token = $response->json('credential.token');

        $this->assertIsString($token);
        $this->assertNotSame('', $token);
        $this->assertDatabaseCount('agent_credentials', 1);
        $this->assertTrue(Hash::check($token, AgentCredential::query()->firstOrFail()->token_hash));

        $secondResponse = $this->postJson('/api/agent/register', [
            'agent_id' => 'agent-api-test-001',
            'hostname' => 'HOST-001-RENAMED',
        ]);

        $secondResponse->assertOk()
            ->assertJsonPath('credential.token', null)
            ->assertJsonPath('credential.returned_once', false);

        $this->assertDatabaseCount('agent_credentials', 1);
    }

    public function test_same_agent_id_updates_existing_device_without_overwriting_custom_label(): void
    {
        Device::query()->create([
            'agent_id' => 'agent-api-test-002',
            'hostname' => 'OLD-HOST',
            'device_label' => 'Editable Label',
            'ip_zerotier' => '10.147.20.10',
        ]);

        $this->postJson('/api/agent/register', [
            'agent_id' => 'agent-api-test-002',
            'hostname' => 'NEW-HOST',
            'zerotier_ip' => '10.147.20.12',
            'windows_user' => 'NEW-HOST\User',
        ])->assertCreated()
            ->assertJsonPath('device.hostname', 'NEW-HOST')
            ->assertJsonPath('device.device_label', 'Editable Label');

        $this->assertDatabaseCount('devices', 1);
        $this->assertDatabaseHas('devices', [
            'agent_id' => 'agent-api-test-002',
            'hostname' => 'NEW-HOST',
            'device_label' => 'Editable Label',
            'ip_zerotier' => '10.147.20.12',
        ]);
    }

    public function test_heartbeat_requires_token(): void
    {
        Device::query()->create([
            'agent_id' => 'agent-api-test-003',
            'hostname' => 'HOST-003',
        ]);

        $this->postJson('/api/agent/heartbeat', [
            'agent_id' => 'agent-api-test-003',
        ])->assertUnauthorized()
            ->assertJsonPath('message', 'Agent token is required.');
    }

    public function test_valid_heartbeat_updates_last_seen_status_and_snapshot(): void
    {
        [$device, $token] = $this->registeredDeviceWithToken('agent-api-test-004', 'HOST-004');

        $this->postJson('/api/agent/heartbeat', [
            'agent_id' => $device->agent_id,
            'hostname' => 'HOST-004-RENAMED',
            'windows_user' => 'HOST-004\User',
            'ip_address' => '192.168.1.44',
            'zerotier_ip' => '10.147.20.44',
            'agent_version' => '1.1.0',
            'rdp_status' => 'available',
            'uptime_seconds' => 7200,
            'last_boot_at' => '2026-05-29T01:00:00+07:00',
        ], [
            'Authorization' => 'Bearer '.$token,
        ])->assertOk()
            ->assertJsonPath('status', 'heartbeat_received')
            ->assertJsonPath('device.agent_status', 'online')
            ->assertJsonPath('device.status', 'online');

        $device->refresh();

        $this->assertSame('HOST-004-RENAMED', $device->hostname);
        $this->assertSame('online', $device->agent_status);
        $this->assertSame('online', $device->status);
        $this->assertNotNull($device->last_seen_at);

        $this->assertDatabaseHas('device_telemetries', [
            'device_id' => $device->id,
            'agent_id' => $device->agent_id,
            'hostname' => 'HOST-004-RENAMED',
            'uptime_seconds' => 7200,
            'agent_status' => 'online',
        ]);

        $this->assertDatabaseMissing('device_telemetries', [
            'device_id' => $device->id,
            'cpu_usage_percent' => 0,
            'ram_usage_percent' => 0,
            'disk_usage_percent' => 0,
        ]);

        $telemetry = DeviceTelemetry::query()->where('device_id', $device->id)->firstOrFail();
        $this->assertNull($telemetry->cpu_usage_percent);
        $this->assertNull($telemetry->ram_usage_percent);
        $this->assertNull($telemetry->disk_usage_percent);
    }

    public function test_heartbeat_stores_cpu_ram_and_disk_telemetry_when_submitted(): void
    {
        [$device, $token] = $this->registeredDeviceWithToken('agent-api-test-telemetry-001', 'HOST-TEL-001');

        $this->postJson('/api/agent/heartbeat', [
            'agent_id' => $device->agent_id,
            'hostname' => 'HOST-TEL-001',
            'cpu_usage_percent' => 37.25,
            'ram_usage_percent' => 64.5,
            'disk_usage_percent' => 71.75,
            'uptime_seconds' => 3600,
            'last_boot_at' => '2026-05-29T02:00:00+07:00',
        ], [
            'Authorization' => 'Bearer '.$token,
        ])->assertOk()
            ->assertJsonPath('status', 'heartbeat_received');

        $telemetry = DeviceTelemetry::query()->where('device_id', $device->id)->firstOrFail();

        $this->assertSame(37.25, $telemetry->cpu_usage_percent);
        $this->assertSame(64.5, $telemetry->ram_usage_percent);
        $this->assertSame(71.75, $telemetry->disk_usage_percent);
        $this->assertSame(3600, $telemetry->uptime_seconds);
        $this->assertSame(37.25, $telemetry->raw_payload['cpu_usage_percent']);
        $this->assertSame(64.5, $telemetry->raw_payload['ram_usage_percent']);
        $this->assertSame(71.75, $telemetry->raw_payload['disk_usage_percent']);
    }

    public function test_heartbeat_preserves_nulls_for_omitted_telemetry_fields(): void
    {
        [$device, $token] = $this->registeredDeviceWithToken('agent-api-test-telemetry-002', 'HOST-TEL-002');

        $this->postJson('/api/agent/heartbeat', [
            'agent_id' => $device->agent_id,
            'hostname' => 'HOST-TEL-002',
            'cpu_usage_percent' => 42,
        ], [
            'Authorization' => 'Bearer '.$token,
        ])->assertOk();

        $telemetry = DeviceTelemetry::query()->where('device_id', $device->id)->firstOrFail();

        $this->assertSame(42.0, $telemetry->cpu_usage_percent);
        $this->assertNull($telemetry->ram_usage_percent);
        $this->assertNull($telemetry->disk_usage_percent);
        $this->assertNull($telemetry->uptime_seconds);
        $this->assertNull($telemetry->last_boot_at);
    }

    public function test_heartbeat_stores_firebird_network_check_when_submitted(): void
    {
        [$device, $token] = $this->registeredDeviceWithToken('agent-api-test-net-001', 'HOST-NET-001');

        $this->postJson('/api/agent/heartbeat', [
            'agent_id' => $device->agent_id,
            'hostname' => 'HOST-NET-001',
            'firebird_check' => [
                'target_type' => 'firebird',
                'target_host' => '10.147.20.5',
                'target_port' => 3051,
                'ping_status' => 'unknown',
                'tcp_status' => 'connected',
                'tcp_latency_ms' => 24,
                'status' => 'normal',
                'checked_at' => '2026-05-29T02:10:00+07:00',
            ],
        ], [
            'Authorization' => 'Bearer '.$token,
        ])->assertOk()
            ->assertJsonPath('status', 'heartbeat_received');

        $check = NetworkCheck::query()->where('device_id', $device->id)->firstOrFail();

        $this->assertSame('firebird', $check->target_type);
        $this->assertSame('10.147.20.5', $check->target_host);
        $this->assertSame(3051, $check->target_port);
        $this->assertSame('connected', $check->tcp_status);
        $this->assertSame(24.0, $check->tcp_latency_ms);
        $this->assertSame('connected', $device->refresh()->firebird_connection_status);
        $this->assertSame('connected', $check->raw_payload['tcp_status']);
    }

    public function test_heartbeat_stores_accurate_process_snapshot_when_submitted(): void
    {
        [$device, $token] = $this->registeredDeviceWithToken('agent-api-test-proc-001', 'HOST-PROC-001');

        $this->postJson('/api/agent/heartbeat', [
            'agent_id' => $device->agent_id,
            'hostname' => 'HOST-PROC-001',
            'accurate_process' => [
                'process_name' => 'accurate.exe',
                'process_status' => 'running',
                'process_pid' => 5420,
                'process_owner' => 'HOST-PROC-001\User',
                'process_path' => 'C:\\Program Files (x86)\\CPSSoft\\ACCURATE5 Enterprise\\accurate.exe',
                'process_started_at' => '2026-05-29T02:05:00+07:00',
                'checked_at' => '2026-05-29T02:10:00+07:00',
            ],
        ], [
            'Authorization' => 'Bearer '.$token,
        ])->assertOk();

        $snapshot = AccurateProcessSnapshot::query()->where('device_id', $device->id)->firstOrFail();

        $this->assertSame('accurate.exe', $snapshot->process_name);
        $this->assertSame('running', $snapshot->process_status);
        $this->assertSame(5420, $snapshot->process_pid);
        $this->assertSame('HOST-PROC-001\User', $snapshot->process_owner);
        $this->assertSame('running', $device->refresh()->accurate_status);
        $this->assertSame('running', $snapshot->raw_payload['process_status']);
    }

    public function test_missing_or_empty_check_payloads_do_not_create_fake_rows(): void
    {
        [$device, $token] = $this->registeredDeviceWithToken('agent-api-test-check-empty-001', 'HOST-EMPTY-001');

        $this->postJson('/api/agent/heartbeat', [
            'agent_id' => $device->agent_id,
            'hostname' => 'HOST-EMPTY-001',
            'firebird_check' => [],
            'accurate_process' => [],
        ], [
            'Authorization' => 'Bearer '.$token,
        ])->assertOk();

        $this->assertDatabaseMissing('network_checks', [
            'device_id' => $device->id,
        ]);
        $this->assertDatabaseMissing('accurate_process_snapshots', [
            'device_id' => $device->id,
        ]);

        $device->refresh();
        $this->assertSame('unknown', $device->firebird_connection_status);
        $this->assertSame('unknown', $device->accurate_status);
    }

    public function test_heartbeat_rejects_invalid_firebird_and_process_check_values(): void
    {
        [$device, $token] = $this->registeredDeviceWithToken('agent-api-test-check-invalid-001', 'HOST-INVALID-001');

        $this->postJson('/api/agent/heartbeat', [
            'agent_id' => $device->agent_id,
            'firebird_check' => [
                'target_port' => 70000,
                'tcp_latency_ms' => -1,
                'tcp_status' => 'maybe',
            ],
            'accurate_process' => [
                'process_status' => 'sleeping',
                'process_pid' => 0,
            ],
        ], [
            'Authorization' => 'Bearer '.$token,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors([
                'firebird_check.target_port',
                'firebird_check.tcp_latency_ms',
                'firebird_check.tcp_status',
                'accurate_process.process_status',
                'accurate_process.process_pid',
            ]);

        $this->assertDatabaseMissing('network_checks', [
            'device_id' => $device->id,
        ]);
        $this->assertDatabaseMissing('accurate_process_snapshots', [
            'device_id' => $device->id,
        ]);
    }

    public function test_heartbeat_rejects_telemetry_values_outside_zero_to_one_hundred(): void
    {
        [$device, $token] = $this->registeredDeviceWithToken('agent-api-test-telemetry-003', 'HOST-TEL-003');

        $this->postJson('/api/agent/heartbeat', [
            'agent_id' => $device->agent_id,
            'cpu_usage_percent' => 100.01,
        ], [
            'Authorization' => 'Bearer '.$token,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('cpu_usage_percent');

        $this->postJson('/api/agent/heartbeat', [
            'agent_id' => $device->agent_id,
            'ram_usage_percent' => -0.01,
        ], [
            'Authorization' => 'Bearer '.$token,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('ram_usage_percent');

        $this->assertDatabaseMissing('device_telemetries', [
            'device_id' => $device->id,
        ]);
    }

    public function test_device_latest_telemetry_relationship_returns_newest_reported_snapshot(): void
    {
        $device = Device::query()->create([
            'agent_id' => 'agent-api-test-telemetry-004',
            'hostname' => 'HOST-TEL-004',
        ]);

        $older = $device->telemetries()->create([
            'agent_id' => $device->agent_id,
            'cpu_usage_percent' => 20,
            'reported_at' => now()->subMinutes(10),
        ]);

        $newer = $device->telemetries()->create([
            'agent_id' => $device->agent_id,
            'cpu_usage_percent' => 80,
            'reported_at' => now(),
        ]);

        $device->refresh();

        $this->assertTrue($device->latestTelemetry->is($newer));
        $this->assertFalse($device->latestTelemetry->is($older));
    }

    public function test_invalid_token_is_rejected(): void
    {
        [$device] = $this->registeredDeviceWithToken('agent-api-test-005', 'HOST-005');

        $this->postJson('/api/agent/heartbeat', [
            'agent_id' => $device->agent_id,
        ], [
            'X-Agent-Token' => 'wrong-token',
        ])->assertUnauthorized()
            ->assertJsonPath('message', 'Agent token is invalid.');
    }

    public function test_pending_commands_requires_valid_token_and_returns_empty_list(): void
    {
        [$device, $token] = $this->registeredDeviceWithToken('agent-api-test-006', 'HOST-006');

        $this->getJson('/api/agent/commands/pending?agent_id='.$device->agent_id)
            ->assertUnauthorized();

        $this->getJson('/api/agent/commands/pending?agent_id='.$device->agent_id, [
            'X-Agent-Token' => $token,
        ])->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('commands', []);
    }

    public function test_agent_api_tests_do_not_depend_on_hardcoded_device_names(): void
    {
        $blockedNames = [
            'WIN'.'-ACC-01',
            'WIN'.'-ACC-02',
            'Laptop'.' Finance',
        ];

        $paths = [
            app_path('Http/Controllers/AgentApiController.php'),
            base_path('routes/api.php'),
        ];

        foreach ($paths as $path) {
            foreach ($blockedNames as $blockedName) {
                $this->assertFileDoesNotContainString($blockedName, $path);
            }
        }
    }

    private function registeredDeviceWithToken(string $agentId, string $hostname): array
    {
        $response = $this->postJson('/api/agent/register', [
            'agent_id' => $agentId,
            'hostname' => $hostname,
        ]);

        $response->assertCreated();

        return [
            Device::query()->where('agent_id', $agentId)->firstOrFail(),
            $response->json('credential.token'),
        ];
    }

    private function assertFileDoesNotContainString(string $needle, string $path): void
    {
        $this->assertStringNotContainsString($needle, file_get_contents($path));
    }
}
