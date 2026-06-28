<?php

namespace App\Console\Commands;

use App\Models\AccurateProcessSnapshot;
use App\Models\AgentCredential;
use App\Models\Alert;
use App\Models\AlertEvidence;
use App\Models\AlertNotification;
use App\Models\Device;
use App\Models\DeviceTelemetry;
use App\Models\Incident;
use App\Models\LogEntry;
use App\Models\NetworkCheck;
use App\Models\RemoteAction;
use App\Models\AccurateAuditSyncRun;
use App\Models\AccurateAuditSyncState;
use App\Models\ParserOffset;
use App\Models\ParserRun;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class WipeDataCommand extends Command
{
    protected $signature = 'data:wipe {--force : Skip confirmation prompt}';
    protected $description = 'Wipe all monitoring data (devices, alerts, incidents, telemetry, logs) but preserve settings, audit, and users.';

    public function handle(): int
    {
        if (! $this->option('force') && ! $this->confirm('This will delete ALL monitoring data. Settings and Accurate Audit events will be preserved. Continue?')) {
            $this->info('Cancelled.');
            return 0;
        }

        $this->info('Wiping data...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $counts = [
            'Alert Evidences' => AlertEvidence::query()->count(),
            'Alert Notifications' => AlertNotification::query()->count(),
            'Accurate Process Snapshots' => AccurateProcessSnapshot::query()->count(),
            'Network Checks' => NetworkCheck::query()->count(),
            'Device Telemetries' => DeviceTelemetry::query()->count(),
            'Log Entries' => LogEntry::query()->count(),
            'Remote Actions' => RemoteAction::query()->count(),
            'Audit Sync Runs' => AccurateAuditSyncRun::query()->count(),
            'Audit Sync States' => AccurateAuditSyncState::query()->count(),
            'Parser Offsets' => ParserOffset::query()->count(),
            'Parser Runs' => ParserRun::query()->count(),
        ];

        AlertEvidence::query()->delete();
        AlertNotification::query()->delete();
        AccurateProcessSnapshot::query()->delete();
        NetworkCheck::query()->delete();
        DeviceTelemetry::query()->delete();
        LogEntry::query()->delete();
        RemoteAction::query()->delete();
        AccurateAuditSyncRun::query()->delete();
        AccurateAuditSyncState::query()->delete();
        ParserOffset::query()->delete();
        ParserRun::query()->delete();

        DB::table('incident_alerts')->delete();
        Incident::query()->delete();

        $alertCount = Alert::query()->count();
        Alert::query()->delete();

        $agentCredCount = AgentCredential::query()->count();
        AgentCredential::query()->delete();

        $deviceCount = Device::query()->count();
        Device::query()->delete();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->table(['Table', 'Deleted'], [
            ...array_map(fn ($k, $v) => [$k, $v], array_keys($counts), $counts),
            ['Alerts', $alertCount],
            ['Agent Credentials', $agentCredCount],
            ['Devices', $deviceCount],
        ]);

        $this->newLine();
        $this->info('All monitoring data wiped. Settings, Accurate Audit Events, and Users are preserved.');
        $this->info('Devices: 0 | Alerts: 0 | Incidents: 0');

        return 0;
    }
}