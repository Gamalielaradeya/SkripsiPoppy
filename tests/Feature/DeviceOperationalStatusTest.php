<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\ThresholdSetting;
use App\Models\User;
use App\Services\Devices\DeviceOperationalStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceOperationalStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_recent_last_seen_is_online(): void
    {
        $this->thresholds();

        $this->assertSame('online', $this->device(now()->subMinutes(4))->display_status);
    }

    public function test_stale_last_seen_is_warning(): void
    {
        $this->thresholds();

        $this->assertSame('warning', $this->device(now()->subMinutes(10))->display_status);
    }

    public function test_old_last_seen_is_offline(): void
    {
        $this->thresholds();

        $this->assertSame('offline', $this->device(now()->subMinutes(20))->display_status);
    }

    public function test_null_last_seen_is_unknown(): void
    {
        $this->thresholds();

        $this->assertSame('unknown', $this->device()->display_status);
    }

    public function test_missing_threshold_settings_use_safe_config_fallback(): void
    {
        config()->set('monitoring.alerting.heartbeat_warning_minutes', 3);
        config()->set('monitoring.alerting.heartbeat_critical_minutes', 8);

        $this->assertSame('warning', $this->device(now()->subMinutes(5))->display_status);
    }

    public function test_dashboard_online_count_uses_computed_display_status(): void
    {
        $this->thresholds();
        $this->device(now(), ['status' => 'offline', 'agent_status' => 'offline']);
        $this->device(now()->subMinutes(20), ['status' => 'online', 'agent_status' => 'online']);

        $response = $this->actingAs(User::factory()->create())->get('/dashboard');

        $response->assertOk();
        $this->assertSame(1, $response->viewData('onlineDevices'));
    }

    private function thresholds(): void
    {
        ThresholdSetting::query()->create([
            'group' => 'device',
            'key' => 'heartbeat_warning_minutes',
            'value' => '5',
            'data_type' => 'integer',
        ]);
        ThresholdSetting::query()->create([
            'group' => 'device',
            'key' => 'heartbeat_critical_minutes',
            'value' => '15',
            'data_type' => 'integer',
        ]);

        app()->forgetInstance(DeviceOperationalStatusService::class);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function device(mixed $lastSeenAt = null, array $overrides = []): Device
    {
        return Device::query()->create($overrides + [
            'agent_id' => 'agent-'.str()->uuid(),
            'hostname' => 'HOST-TEST',
            'agent_status' => 'online',
            'status' => 'online',
            'last_seen_at' => $lastSeenAt,
            'is_active' => true,
        ]);
    }
}
