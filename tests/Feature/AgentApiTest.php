<?php

namespace Tests\Feature;

use App\Models\AgentCredential;
use App\Models\Device;
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
