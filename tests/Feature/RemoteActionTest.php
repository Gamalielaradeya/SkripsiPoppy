<?php

namespace Tests\Feature;

use App\Models\AgentCredential;
use App\Models\Device;
use App\Models\RemoteAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RemoteActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_restart_action_only_with_reason_and_confirmation(): void
    {
        $user = $this->adminUser();
        $device = $this->device('agent-restart-web-001');

        $this->actingAs($user)
            ->post(route('devices.remote-actions.restart', $device), [
                'confirm_restart' => '1',
            ])
            ->assertSessionHasErrors('reason');

        $this->actingAs($user)
            ->post(route('devices.remote-actions.restart', $device), [
                'reason' => 'Restart requested after user confirmation.',
            ])
            ->assertSessionHasErrors('confirm_restart');

        $this->actingAs($user)
            ->post(route('devices.remote-actions.restart', $device), [
                'reason' => 'Restart requested after user confirmation.',
                'confirm_restart' => '1',
            ])
            ->assertRedirect();

        $action = RemoteAction::query()->where('device_id', $device->id)->firstOrFail();

        $this->assertSame(RemoteAction::ACTION_RESTART_CLIENT, $action->action_type);
        $this->assertSame(RemoteAction::STATUS_PENDING, $action->status);
        $this->assertSame($user->id, $action->requested_by);
        $this->assertSame($user->id, $action->confirmed_by);
        $this->assertSame('Restart requested after user confirmation.', $action->reason);
        $this->assertTrue($action->requires_confirmation);
        $this->assertNotNull($action->confirmed_at);
        $this->assertNotNull($action->requested_at);
        $this->assertNotNull($action->expires_at);
    }

    public function test_rdp_action_logs_open_rdp_and_shows_safe_launcher_output(): void
    {
        $user = $this->adminUser();
        $device = $this->device('agent-rdp-web-001', [
            'ip_zerotier' => '10.147.20.91',
            'ip_local' => '192.168.10.91',
        ]);

        $this->actingAs($user)
            ->post(route('devices.remote-actions.rdp', $device))
            ->assertRedirect();

        $action = RemoteAction::query()->where('device_id', $device->id)->firstOrFail();

        $this->assertSame(RemoteAction::ACTION_OPEN_RDP, $action->action_type);
        $this->assertSame(RemoteAction::STATUS_SUCCEEDED, $action->status);
        $this->assertSame('10.147.20.91', $action->payload['target_ip']);
        $this->assertSame('mstsc /v:10.147.20.91', $action->payload['mstsc_command']);

        $this->actingAs($user)
            ->get(route('remote-actions.show', $action))
            ->assertOk()
            ->assertSee('mstsc /v:10.147.20.91')
            ->assertSee('No credentials included')
            ->assertDontSee('password')
            ->assertDontSee('username');

        $rdpFile = $this->actingAs($user)->get(route('remote-actions.rdp-file', $action));
        $rdpFile->assertOk()
            ->assertSee('full address:s:10.147.20.91', false)
            ->assertSee('prompt for credentials:i:1', false)
            ->assertDontSee('password');
    }

    public function test_rdp_launcher_falls_back_to_local_ip_and_is_unavailable_without_ip(): void
    {
        $user = $this->adminUser();
        $localOnly = $this->device('agent-rdp-web-002', ['ip_local' => '192.168.10.92']);
        $withoutIp = $this->device('agent-rdp-web-003');

        $this->actingAs($user)
            ->post(route('devices.remote-actions.rdp', $localOnly))
            ->assertRedirect();

        $this->assertDatabaseHas('remote_actions', [
            'device_id' => $localOnly->id,
            'action_type' => RemoteAction::ACTION_OPEN_RDP,
        ]);
        $this->assertSame('192.168.10.92', RemoteAction::query()->where('device_id', $localOnly->id)->firstOrFail()->payload['target_ip']);

        $this->actingAs($user)
            ->post(route('devices.remote-actions.rdp', $withoutIp))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('remote_actions', [
            'device_id' => $withoutIp->id,
            'action_type' => RemoteAction::ACTION_OPEN_RDP,
        ]);
    }

    public function test_agent_pending_commands_requires_valid_token_and_returns_only_own_unexpired_restart_commands(): void
    {
        $user = $this->adminUser();
        [$deviceA, $tokenA] = $this->registeredDeviceWithToken('agent-command-001');
        [$deviceB] = $this->registeredDeviceWithToken('agent-command-002');

        $ownCommand = $this->remoteAction($deviceA, $user, [
            'status' => RemoteAction::STATUS_PENDING,
            'action_type' => RemoteAction::ACTION_RESTART_CLIENT,
            'expires_at' => now()->addMinutes(5),
        ]);
        $this->remoteAction($deviceB, $user, [
            'status' => RemoteAction::STATUS_PENDING,
            'action_type' => RemoteAction::ACTION_RESTART_CLIENT,
            'expires_at' => now()->addMinutes(5),
        ]);
        $this->remoteAction($deviceA, $user, [
            'status' => RemoteAction::STATUS_PENDING,
            'action_type' => RemoteAction::ACTION_RESTART_CLIENT,
            'expires_at' => now()->subMinute(),
        ]);
        $unsupported = $this->remoteAction($deviceA, $user, [
            'status' => RemoteAction::STATUS_PENDING,
            'action_type' => RemoteAction::ACTION_PING_TEST,
            'expires_at' => now()->addMinutes(5),
        ]);

        $this->getJson('/api/agent/commands/pending?agent_id='.$deviceA->agent_id)
            ->assertUnauthorized();

        $this->getJson('/api/agent/commands/pending?agent_id='.$deviceA->agent_id, [
            'X-Agent-Token' => 'wrong-token',
        ])->assertUnauthorized();

        $response = $this->getJson('/api/agent/commands/pending?agent_id='.$deviceA->agent_id, [
            'Authorization' => 'Bearer '.$tokenA,
        ]);

        $response->assertOk()
            ->assertJsonCount(1, 'commands')
            ->assertJsonPath('commands.0.id', $ownCommand->id)
            ->assertJsonPath('commands.0.action_type', RemoteAction::ACTION_RESTART_CLIENT)
            ->assertJsonPath('commands.0.status', RemoteAction::STATUS_PICKED_UP);

        $this->assertSame(RemoteAction::STATUS_PICKED_UP, $ownCommand->refresh()->status);
        $this->assertNotNull($ownCommand->picked_up_at);
        $this->assertSame(RemoteAction::STATUS_PENDING, $unsupported->refresh()->status);
    }

    public function test_agent_result_endpoint_updates_only_commands_owned_by_authenticated_device(): void
    {
        $user = $this->adminUser();
        [$deviceA, $tokenA] = $this->registeredDeviceWithToken('agent-result-001');
        [$deviceB, $tokenB] = $this->registeredDeviceWithToken('agent-result-002');

        $action = $this->remoteAction($deviceA, $user, [
            'status' => RemoteAction::STATUS_PICKED_UP,
            'action_type' => RemoteAction::ACTION_RESTART_CLIENT,
            'picked_up_at' => now(),
            'expires_at' => now()->addMinutes(5),
        ]);

        $this->postJson(route('api.agent.commands.result', $action), [
            'agent_id' => $deviceB->agent_id,
            'status' => RemoteAction::STATUS_SUCCEEDED,
            'result_message' => 'restart scheduled',
        ], [
            'Authorization' => 'Bearer '.$tokenB,
        ])->assertForbidden();

        $this->assertSame(RemoteAction::STATUS_PICKED_UP, $action->refresh()->status);
        $this->assertNull($action->completed_at);

        $this->postJson(route('api.agent.commands.result', $action), [
            'agent_id' => $deviceA->agent_id,
            'status' => RemoteAction::STATUS_SUCCEEDED,
            'result_message' => 'restart scheduled',
            'executed_at' => now()->toIso8601String(),
        ], [
            'Authorization' => 'Bearer '.$tokenA,
        ])->assertOk()
            ->assertJsonPath('status', 'result_recorded')
            ->assertJsonPath('command.status', RemoteAction::STATUS_SUCCEEDED);

        $action->refresh();

        $this->assertSame(RemoteAction::STATUS_SUCCEEDED, $action->status);
        $this->assertSame('restart scheduled', $action->result_message);
        $this->assertNotNull($action->executed_at);
        $this->assertNotNull($action->completed_at);
    }

    private function adminUser(): User
    {
        return User::query()->create([
            'name' => 'Administrator',
            'email' => uniqid('admin-', true).'@example.com',
            'password' => Hash::make('password'),
        ]);
    }

    private function device(string $agentId, array $overrides = []): Device
    {
        return Device::query()->create(array_merge([
            'agent_id' => $agentId,
            'hostname' => strtoupper($agentId),
            'device_label' => 'Device '.$agentId,
        ], $overrides));
    }

    private function registeredDeviceWithToken(string $agentId): array
    {
        $device = $this->device($agentId);
        $token = 'token-'.$agentId;

        AgentCredential::query()->create([
            'device_id' => $device->id,
            'token_name' => 'test-token',
            'token_hash' => Hash::make($token),
        ]);

        return [$device, $token];
    }

    private function remoteAction(Device $device, User $user, array $overrides = []): RemoteAction
    {
        return RemoteAction::query()->create(array_merge([
            'device_id' => $device->id,
            'requested_by' => $user->id,
            'action_type' => RemoteAction::ACTION_RESTART_CLIENT,
            'status' => RemoteAction::STATUS_PENDING,
            'reason' => 'Manual action for test.',
            'requires_confirmation' => true,
            'confirmed_by' => $user->id,
            'confirmed_at' => now(),
            'payload' => ['restart_delay_seconds' => 30],
            'requested_at' => now(),
            'expires_at' => now()->addMinutes(10),
        ], $overrides));
    }
}
