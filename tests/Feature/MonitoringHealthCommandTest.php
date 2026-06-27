<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class MonitoringHealthCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_monitoring_health_command_reports_pass_warn_style_output(): void
    {
        $path = storage_path('framework/testing/health-rsyslog');
        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }

        config([
            'monitoring.rsyslog_remote_log_path' => $path,
            'monitoring.parser_batch_limit' => 25,
            'monitoring.telegram.enabled' => false,
        ]);

        $exitCode = Artisan::call('monitoring:health');
        $output = Artisan::output();

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('[PASS] Laravel booted', $output);
        $this->assertStringContainsString('[PASS] Database connection available', $output);
        $this->assertStringContainsString('[PASS] RSyslog path readable', $output);
        $this->assertStringContainsString('[PASS] Command available: rsyslog:parse', $output);
        $this->assertStringContainsString('[PASS] Command available: accurate:audit-sync', $output);
        $this->assertStringContainsString('[PASS] Command available: alerts:detect', $output);
        $this->assertStringContainsString('[PASS] Command available: monitoring:health', $output);
        $this->assertStringContainsString('[WARN] Telegram alerting disabled', $output);
    }

    public function test_monitoring_health_command_does_not_print_sensitive_values(): void
    {
        config([
            'monitoring.telegram.enabled' => true,
            'monitoring.telegram.bot_token' => '123456:secret-telegram-token',
            'monitoring.telegram.chat_id' => '987654321',
            'monitoring.accurate_audit.firebird_password' => 'secret-firebird-password',
        ]);

        $exitCode = Artisan::call('monitoring:health');
        $output = Artisan::output();

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('[PASS] Telegram bot token configured - present', $output);
        $this->assertStringContainsString('[PASS] Telegram chat id configured - present', $output);
        $this->assertStringNotContainsString('123456:secret-telegram-token', $output);
        $this->assertStringNotContainsString('987654321', $output);
        $this->assertStringNotContainsString('secret-firebird-password', $output);
    }
}
