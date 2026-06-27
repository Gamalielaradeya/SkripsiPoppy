<?php

namespace Tests\Feature;

use App\Models\AccurateAuditEvent;
use App\Models\AccurateAuditSource;
use App\Models\Alert;
use App\Models\Device;
use App\Models\Incident;
use App\Models\RemoteAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_monitoring_foundation_tables_exist(): void
    {
        $tables = [
            'devices',
            'agent_credentials',
            'device_telemetries',
            'network_checks',
            'accurate_process_snapshots',
            'server_service_checks',
            'logs',
            'parser_offsets',
            'parser_runs',
            'threshold_settings',
            'system_settings',
            'alerts',
            'alert_evidences',
            'alert_notifications',
            'incidents',
            'incident_alerts',
            'remote_actions',
            'accurate_audit_sources',
            'accurate_audit_events',
            'accurate_audit_sync_states',
            'accurate_audit_sync_runs',
        ];

        foreach ($tables as $table) {
            $this->assertTrue(Schema::hasTable($table), "Expected table [{$table}] to exist.");
        }
    }

    public function test_device_and_log_identity_columns_exist(): void
    {
        $this->assertTrue(Schema::hasColumns('devices', [
            'agent_id',
            'hostname',
            'device_label',
            'last_seen_at',
        ]));

        $this->assertTrue(Schema::hasColumns('logs', [
            'raw_message',
            'parsed_message',
            'event_type',
            'source',
            'category',
            'severity',
            'target_type',
            'target_id',
            'target_name',
            'hash',
        ]));
    }

    public function test_seeders_create_thresholds_and_placeholder_settings_only(): void
    {
        $this->seed();

        $this->assertDatabaseHas('threshold_settings', [
            'key' => 'heartbeat_warning_minutes',
            'value' => '5',
        ]);

        $this->assertDatabaseHas('threshold_settings', [
            'key' => 'firebird_latency_critical_ms',
            'value' => '1500',
        ]);

        $this->assertDatabaseHas('system_settings', [
            'key' => 'telegram_bot_token_env_key',
            'value' => 'TELEGRAM_BOT_TOKEN',
            'is_sensitive' => true,
        ]);

        $this->assertDatabaseCount('devices', 0);
        $this->assertDatabaseCount('logs', 0);
        $this->assertDatabaseCount('alerts', 0);
        $this->assertDatabaseCount('incidents', 0);
        $this->assertDatabaseCount('remote_actions', 0);
        $this->assertDatabaseCount('accurate_audit_events', 0);
    }

    public function test_milestone_2b_models_can_be_created_with_relationships(): void
    {
        $device = Device::query()->create([
            'agent_id' => 'test-agent-001',
            'hostname' => 'TEST-HOST',
            'device_label' => 'Test Device',
        ]);

        $user = User::query()->create([
            'name' => 'Test Admin',
            'email' => 'test-admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $auditSource = AccurateAuditSource::query()->create([
            'name' => 'Test Firebird Source',
            'firebird_host' => '127.0.0.1',
            'database_path' => 'placeholder.fdb',
            'credential_ref' => 'ACCURATE_FIREBIRD_PASSWORD',
        ]);

        $auditEvent = AccurateAuditEvent::query()->create([
            'accurate_audit_source_id' => $auditSource->id,
            'accurate_audit_id' => '1001',
            'activity_time' => now(),
            'accurate_username' => 'TESTUSER',
            'accurate_fullname' => 'Test User',
            'source' => 'SALES',
            'transaction_type' => 'UPDATE',
            'transaction_description' => 'Synthetic test audit event.',
            'hash' => hash('sha256', 'test-audit-event-1001'),
        ]);

        $alert = Alert::query()->create([
            'device_id' => $device->id,
            'accurate_audit_event_id' => $auditEvent->id,
            'alert_code' => 'CPU_HIGH',
            'target_type' => 'device',
            'target_id' => (string) $device->id,
            'target_name' => $device->display_name,
            'category' => 'performance',
            'severity' => 'warning',
            'title' => 'CPU tinggi pada Test Device',
            'description' => 'Synthetic test alert with full context.',
            'detected_by' => 'Database foundation test',
            'source' => 'test',
            'impact' => 'Device can become slow.',
            'recommended_action' => 'Investigate the device manually.',
            'detected_at' => now(),
        ]);

        $alert->evidences()->create([
            'evidence_key' => 'cpu_percent',
            'evidence_value' => '87',
            'evidence_type' => 'number',
            'source' => 'test',
            'measured_at' => now(),
        ]);

        $incident = Incident::query()->create([
            'device_id' => $device->id,
            'incident_code' => 'DEVICE_SLOW_INDICATION',
            'target_type' => 'device',
            'target_id' => (string) $device->id,
            'target_name' => $device->display_name,
            'severity' => 'warning',
            'title' => 'Device lambat pada Test Device',
            'summary' => 'Synthetic test incident linked to one alert.',
            'detected_at' => now(),
        ]);

        $incident->alerts()->attach($alert);

        RemoteAction::query()->create([
            'device_id' => $device->id,
            'requested_by' => $user->id,
            'action_type' => RemoteAction::ACTION_RESTART_CLIENT,
            'status' => 'requested',
            'reason' => 'Synthetic test restart request.',
            'requires_confirmation' => true,
            'confirmed_by' => $user->id,
            'confirmed_at' => now(),
            'requested_at' => now(),
            'expires_at' => now()->addMinutes(10),
        ]);

        $this->assertTrue($device->alerts()->exists());
        $this->assertSame('87', $alert->evidences()->first()->evidence_value);
        $this->assertTrue($incident->alerts()->whereKey($alert->id)->exists());
        $this->assertTrue($auditSource->events()->whereKey($auditEvent->id)->exists());
        $this->assertTrue($device->remoteActions()->exists());
    }
}
