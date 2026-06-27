<?php

namespace Tests\Feature;

use App\Models\AccurateAuditEvent;
use App\Models\AccurateAuditSource;
use App\Models\Alert;
use App\Models\Device;
use App\Services\Alerts\AlertDetectionService;
use Database\Seeders\ThresholdSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AlertDetectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ThresholdSettingSeeder::class);
        config()->set('monitoring.telegram.enabled', false);
        Http::fake();
    }

    public function test_alert_detection_creates_contextual_device_offline_alert_with_evidence(): void
    {
        $device = $this->device([
            'last_seen_at' => now()->subMinutes(20),
        ]);

        $exitCode = Artisan::call('alerts:detect');

        $this->assertSame(0, $exitCode);

        $alert = Alert::query()->where('alert_code', 'DEVICE_OFFLINE')->firstOrFail();

        $this->assertSame('critical', $alert->severity);
        $this->assertSame('device', $alert->category);
        $this->assertSame('device', $alert->target_type);
        $this->assertSame((string) $device->id, $alert->target_id);
        $this->assertSame($device->display_name, $alert->target_name);
        $this->assertNotEmpty($alert->detected_by);
        $this->assertNotEmpty($alert->source);
        $this->assertNotEmpty($alert->evidence_summary);
        $this->assertNotEmpty($alert->impact);
        $this->assertNotEmpty($alert->recommended_action);
        $this->assertSame('open', $alert->status);
        $this->assertNotEmpty($alert->dedupe_key);
        $this->assertNotNull($alert->detected_at);
        $this->assertDatabaseHas('alert_evidences', [
            'alert_id' => $alert->id,
            'evidence_key' => 'minutes_since_last_seen',
        ]);
        $this->assertDatabaseHas('alert_notifications', [
            'alert_id' => $alert->id,
            'channel' => 'telegram',
            'status' => 'skipped',
        ]);
        Http::assertNothingSent();
    }

    public function test_cooldown_and_dedupe_prevent_duplicate_spam(): void
    {
        $this->device([
            'last_seen_at' => now()->subMinutes(20),
        ]);

        app(AlertDetectionService::class)->detect();
        app(AlertDetectionService::class)->detect();

        $this->assertDatabaseCount('alerts', 1);
        $this->assertSame(1, Alert::query()->firstOrFail()->notifications()->count());
    }

    public function test_cpu_ram_and_disk_rules_use_stored_telemetry_rows(): void
    {
        $device = $this->device();
        $device->telemetries()->create([
            'agent_id' => $device->agent_id,
            'hostname' => $device->hostname,
            'cpu_usage_percent' => 91,
            'ram_usage_percent' => 86,
            'disk_usage_percent' => 82,
            'reported_at' => now(),
        ]);

        app(AlertDetectionService::class)->detect();

        $this->assertDatabaseHas('alerts', ['alert_code' => 'CPU_CRITICAL', 'target_id' => (string) $device->id]);
        $this->assertDatabaseHas('alerts', ['alert_code' => 'RAM_HIGH', 'target_id' => (string) $device->id]);
        $this->assertDatabaseHas('alerts', ['alert_code' => 'DISK_HIGH', 'target_id' => (string) $device->id]);
        $this->assertDatabaseHas('alert_evidences', ['evidence_key' => 'cpu_usage_percent', 'evidence_value' => '91']);
        $this->assertDatabaseHas('alert_evidences', ['evidence_key' => 'ram_usage_percent', 'evidence_value' => '86']);
        $this->assertDatabaseHas('alert_evidences', ['evidence_key' => 'disk_usage_percent', 'evidence_value' => '82']);
    }

    public function test_cpu_warning_rule_is_created_below_critical_threshold(): void
    {
        $device = $this->device();
        $device->telemetries()->create([
            'agent_id' => $device->agent_id,
            'hostname' => $device->hostname,
            'cpu_usage_percent' => 84,
            'reported_at' => now(),
        ]);

        app(AlertDetectionService::class)->detect();

        $this->assertDatabaseHas('alerts', ['alert_code' => 'CPU_HIGH', 'severity' => 'warning']);
        $this->assertDatabaseMissing('alerts', ['alert_code' => 'CPU_CRITICAL']);
    }

    public function test_firebird_connectivity_failed_rule_uses_network_check_evidence(): void
    {
        $device = $this->device();
        $device->networkChecks()->create([
            'target_type' => 'firebird',
            'target_name' => 'Firebird VPS',
            'target_host' => '10.147.20.5',
            'target_port' => 3051,
            'tcp_status' => 'timeout',
            'tcp_latency_ms' => null,
            'status' => 'error',
            'raw_payload' => ['failure_reason' => 'tcp timeout'],
            'checked_at' => now(),
        ]);

        app(AlertDetectionService::class)->detect();

        $alert = Alert::query()->where('alert_code', 'FIREBIRD_CONNECTIVITY_FAILED')->firstOrFail();

        $this->assertSame('device_firebird', $alert->target_type);
        $this->assertStringContainsString('10.147.20.5:3051', $alert->target_name);
        $this->assertStringContainsString('tcp timeout', $alert->evidence_summary);
        $this->assertDatabaseHas('alert_evidences', [
            'alert_id' => $alert->id,
            'evidence_key' => 'tcp_status',
            'evidence_value' => 'timeout',
        ]);
    }

    public function test_firebird_latency_high_rule_uses_network_check_evidence(): void
    {
        $device = $this->device();
        $device->networkChecks()->create([
            'target_type' => 'firebird',
            'target_host' => '10.147.20.5',
            'target_port' => 3051,
            'tcp_status' => 'connected',
            'tcp_latency_ms' => 650,
            'status' => 'normal',
            'checked_at' => now(),
        ]);

        app(AlertDetectionService::class)->detect();

        $this->assertDatabaseHas('alerts', [
            'alert_code' => 'FIREBIRD_LATENCY_HIGH',
            'severity' => 'warning',
        ]);
        $this->assertDatabaseHas('alert_evidences', [
            'evidence_key' => 'tcp_latency_ms',
            'evidence_value' => '650',
        ]);
    }

    public function test_accurate_process_not_running_rule_uses_snapshot_evidence(): void
    {
        $device = $this->device([
            'accurate_status' => 'not_running',
        ]);
        $device->accurateProcessSnapshots()->create([
            'process_name' => 'accurate.exe',
            'process_status' => 'not_running',
            'checked_at' => now(),
        ]);

        app(AlertDetectionService::class)->detect();

        $alert = Alert::query()->where('alert_code', 'ACCURATE_PROCESS_NOT_RUNNING')->firstOrFail();

        $this->assertSame((string) $device->id, $alert->target_id);
        $this->assertStringContainsString('accurate.exe', $alert->evidence_summary);
        $this->assertDatabaseHas('alert_evidences', [
            'alert_id' => $alert->id,
            'evidence_key' => 'process_status',
            'evidence_value' => 'not_running',
        ]);
    }

    public function test_accurate_audit_delete_rule_only_uses_explicit_delete_transaction_type(): void
    {
        $source = AccurateAuditSource::query()->create([
            'name' => 'Test Accurate Source',
            'firebird_host' => '127.0.0.1',
            'database_path' => 'placeholder.fdb',
            'credential_ref' => 'ACCURATE_FIREBIRD_PASSWORD',
        ]);

        AccurateAuditEvent::query()->create([
            'accurate_audit_source_id' => $source->id,
            'accurate_audit_id' => '1001',
            'activity_time' => now(),
            'accurate_username' => 'FINANCE01',
            'source' => 'SALES',
            'transaction_type' => 'DELETE',
            'transaction_description' => 'Delete sales invoice',
            'invoice_no' => 'SI-001',
            'hash' => hash('sha256', 'delete-event'),
        ]);

        AccurateAuditEvent::query()->create([
            'accurate_audit_source_id' => $source->id,
            'accurate_audit_id' => '1002',
            'activity_time' => now(),
            'accurate_username' => 'FINANCE01',
            'source' => 'SALES',
            'transaction_type' => 'UPDATE',
            'transaction_description' => 'Update sales invoice',
            'hash' => hash('sha256', 'update-event'),
        ]);

        app(AlertDetectionService::class)->detect();

        $this->assertDatabaseHas('alerts', ['alert_code' => 'ACCURATE_AUDIT_DELETE']);
        $this->assertDatabaseCount('alerts', 1);
        $this->assertSame(['ACCURATE_AUDIT_DELETE'], Alert::query()->pluck('alert_code')->all());
    }

    private function device(array $overrides = []): Device
    {
        return Device::query()->create($overrides + [
            'agent_id' => 'agent-'.str()->uuid(),
            'hostname' => 'HOST-TEST',
            'device_label' => 'Device Test',
            'windows_user' => 'HOST-TEST\\User',
            'agent_status' => 'online',
            'status' => 'online',
            'last_seen_at' => now(),
            'is_active' => true,
        ]);
    }
}
