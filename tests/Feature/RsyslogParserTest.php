<?php

namespace Tests\Feature;

use App\Models\LogEntry;
use App\Models\ParserOffset;
use App\Services\Rsyslog\StructuredSyslogLineParser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class RsyslogParserTest extends TestCase
{
    use RefreshDatabase;

    public function test_rsyslog_parse_command_exists(): void
    {
        $exitCode = Artisan::call('rsyslog:parse', [
            '--path' => storage_path('framework/testing/missing-rsyslog-path'),
        ]);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('RSyslog path not found', Artisan::output());
    }

    public function test_parser_parses_key_value_logs_with_quoted_values(): void
    {
        $parsed = app(StructuredSyslogLineParser::class)->parse(
            '2026-05-29T10:15:00+07:00 DESKTOP-01 device-monitor: event_type=device_heartbeat agent_id=agent-001 hostname=DESKTOP-01 windows_user="DOMAIN\\Finance User" status=online',
        );

        $this->assertNotNull($parsed);
        $this->assertSame('device-monitor', $parsed['source']);
        $this->assertSame('device_heartbeat', $parsed['event_type']);
        $this->assertSame('agent-001', $parsed['agent_id']);
        $this->assertSame('DOMAIN\\Finance User', $parsed['parsed_payload']['windows_user']);
    }

    public function test_parser_stores_raw_message_and_parsed_payload(): void
    {
        $file = $this->writeRsyslogFile([
            '2026-05-29T10:15:00+07:00 DESKTOP-01 device-monitor: event_type=device_heartbeat agent_id=agent-001 hostname=DESKTOP-01 windows_user="DOMAIN\\Finance User" status=online',
        ]);

        Artisan::call('rsyslog:parse', ['--path' => $file]);

        $log = LogEntry::query()->firstOrFail();

        $this->assertSame('device-monitor', $log->source);
        $this->assertSame('device_heartbeat', $log->event_type);
        $this->assertSame('agent-001', $log->agent_id);
        $this->assertSame('DESKTOP-01', $log->hostname);
        $this->assertSame('device', $log->category);
        $this->assertSame('info', $log->severity);
        $this->assertStringContainsString('device-monitor:', $log->raw_message);
        $this->assertSame('DOMAIN\\Finance User', $log->parsed_payload['windows_user']);
        $this->assertSame(realpath($file), $log->source_file);
        $this->assertNotNull($log->hash);
    }

    public function test_parser_prevents_duplicate_inserts_with_hash(): void
    {
        $line = '2026-05-29T10:16:00+07:00 DESKTOP-01 perf-monitor: event_type=performance_status agent_id=agent-001 hostname=DESKTOP-01 cpu_usage_percent=91 ram_usage_percent=60 disk_usage_percent=40';
        $file = $this->writeRsyslogFile([$line]);

        Artisan::call('rsyslog:parse', ['--path' => $file]);
        ParserOffset::query()->update(['last_position' => 0]);
        Artisan::call('rsyslog:parse', ['--path' => $file]);

        $this->assertDatabaseCount('logs', 1);
        $this->assertSame('critical', LogEntry::query()->firstOrFail()->severity);
    }

    public function test_parser_offset_reads_only_new_lines(): void
    {
        $file = $this->writeRsyslogFile([
            '2026-05-29T10:17:00+07:00 DESKTOP-01 heartbeat-monitor: event_type=heartbeat_status agent_id=agent-001 hostname=DESKTOP-01 uptime_seconds=100 last_boot_at="2026-05-29T08:00:00Z"',
        ]);

        Artisan::call('rsyslog:parse', ['--path' => $file]);
        $firstOffset = ParserOffset::query()->firstOrFail()->last_position;

        file_put_contents(
            $file,
            PHP_EOL.'2026-05-29T10:18:00+07:00 DESKTOP-01 heartbeat-monitor: event_type=heartbeat_status agent_id=agent-001 hostname=DESKTOP-01 uptime_seconds=160 last_boot_at="2026-05-29T08:00:00Z"',
            FILE_APPEND,
        );

        Artisan::call('rsyslog:parse', ['--path' => $file]);
        $secondOffset = ParserOffset::query()->firstOrFail()->last_position;

        $this->assertDatabaseCount('logs', 2);
        $this->assertGreaterThan($firstOffset, $secondOffset);
    }

    public function test_agent_dry_run_shows_syslog_lines_when_enabled(): void
    {
        if (! $this->powershellAvailable()) {
            $this->markTestSkipped('PowerShell is not available in this test environment.');
        }

        $runtime = storage_path('framework/testing/windows-agent-runtime');
        if (! is_dir($runtime)) {
            mkdir($runtime, 0777, true);
        }

        $configPath = storage_path('framework/testing/windows-agent-syslog-config.json');
        file_put_contents($configPath, json_encode([
            'api_base_url' => 'http://127.0.0.1:8000/api/agent',
            'agent_version' => '1.0.0-test',
            'runtime_path' => $runtime,
            'monitored_drive' => 'C:',
            'rdp_port' => 3389,
            'request_timeout_seconds' => 1,
            'syslog_enabled' => true,
            'syslog_host' => '127.0.0.1',
            'syslog_port' => 5514,
            'syslog_protocol' => 'udp',
            'syslog_app_name' => 'centralized-monitoring-agent-test',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $agentPath = base_path('windows-agent/agent.ps1');
        $command = 'powershell.exe -NoProfile -ExecutionPolicy Bypass -File '.escapeshellarg($agentPath)
            .' -ConfigPath '.escapeshellarg($configPath).' -DryRun';

        exec($command.' 2>&1', $output, $exitCode);
        $text = implode("\n", $output);

        $this->assertSame(0, $exitCode, $text);
        $this->assertStringContainsString('Syslog dry-run lines:', $text);
        $this->assertStringContainsString('device-monitor: event_type=device_heartbeat', $text);
        $this->assertStringContainsString('perf-monitor: event_type=performance_status', $text);
        $this->assertStringContainsString('heartbeat-monitor: event_type=heartbeat_status', $text);
        $this->assertStringNotContainsString('agent-token', $text);
    }

    private function writeRsyslogFile(array $lines): string
    {
        $dir = storage_path('framework/testing/rsyslog-parser');
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $file = $dir.'/all.log';
        file_put_contents($file, implode(PHP_EOL, $lines).PHP_EOL);

        return $file;
    }

    private function powershellAvailable(): bool
    {
        exec('powershell.exe -NoProfile -Command "$PSVersionTable.PSVersion.Major" 2>NUL', $output, $exitCode);

        return $exitCode === 0 && $output !== [];
    }
}
