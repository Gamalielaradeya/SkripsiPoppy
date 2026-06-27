<?php

namespace Tests\Feature;

use App\Models\Alert;
use App\Models\Device;
use App\Services\Alerts\TelegramAlertService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramAlertServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_telegram_disabled_records_skipped_without_http_call(): void
    {
        config()->set('monitoring.telegram.enabled', false);
        Http::fake();

        $alert = $this->alert();
        $notification = app(TelegramAlertService::class)->sendContextualAlert($alert);

        $this->assertSame('skipped', $notification->status);
        $this->assertSame('Telegram alert is disabled.', $notification->error_message);
        Http::assertNothingSent();
    }

    public function test_telegram_enabled_sends_contextual_message_and_records_sent(): void
    {
        config()->set('monitoring.telegram.enabled', true);
        config()->set('monitoring.telegram.bot_token', 'telegram-token-placeholder');
        config()->set('monitoring.telegram.chat_id', 'telegram-chat-placeholder');
        config()->set('monitoring.accurate_audit.firebird_password', 'firebird-password-placeholder');
        config()->set('monitoring.accurate_audit.firebird_database', 'C:\\secret\\ACCURATE.FDB');

        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        $alert = $this->alert();
        $notification = app(TelegramAlertService::class)->sendContextualAlert($alert);

        $this->assertSame('sent', $notification->status);
        $this->assertSame('telegram:configured', $notification->recipient);
        $this->assertNotNull($notification->sent_at);
        $this->assertStringContainsString('[CRITICAL] CPU sangat tinggi pada Device Test', $notification->message);
        $this->assertStringContainsString('Target: Device Test', $notification->message);
        $this->assertStringContainsString('Evidence: CPU 91%, threshold 90%.', $notification->message);
        $this->assertStringContainsString('Impact:', $notification->message);
        $this->assertStringContainsString('Recommended action:', $notification->message);
        $this->assertStringNotContainsString('telegram-token-placeholder', $notification->message);
        $this->assertStringNotContainsString('telegram-chat-placeholder', $notification->message);
        $this->assertStringNotContainsString('firebird-password-placeholder', $notification->message);
        $this->assertStringNotContainsString('ACCURATE.FDB', $notification->message);

        Http::assertSent(fn ($request): bool => $request['chat_id'] === 'telegram-chat-placeholder'
            && str_contains($request['text'], 'Detected by: Windows Agent'));
    }

    public function test_telegram_failure_records_safe_error_message(): void
    {
        config()->set('monitoring.telegram.enabled', true);
        config()->set('monitoring.telegram.bot_token', 'telegram-token-placeholder');
        config()->set('monitoring.telegram.chat_id', 'telegram-chat-placeholder');

        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => false], 500),
        ]);

        $notification = app(TelegramAlertService::class)->sendContextualAlert($this->alert());

        $this->assertSame('failed', $notification->status);
        $this->assertSame('Telegram API returned HTTP 500.', $notification->error_message);
        $this->assertStringNotContainsString('telegram-token-placeholder', (string) $notification->error_message);
        $this->assertStringNotContainsString('telegram-chat-placeholder', (string) $notification->error_message);
    }

    private function alert(): Alert
    {
        $device = Device::query()->create([
            'agent_id' => 'agent-telegram-test',
            'hostname' => 'HOST-TG',
            'device_label' => 'Device Test',
            'agent_status' => 'online',
            'status' => 'online',
            'last_seen_at' => now(),
        ]);

        return Alert::query()->create([
            'device_id' => $device->id,
            'alert_code' => 'CPU_CRITICAL',
            'target_type' => 'device',
            'target_id' => (string) $device->id,
            'target_name' => $device->display_name,
            'category' => 'performance',
            'severity' => 'critical',
            'title' => 'CPU sangat tinggi pada Device Test',
            'description' => 'CPU melewati threshold critical.',
            'detected_by' => 'Windows Agent',
            'source' => 'device_telemetries',
            'evidence_summary' => 'CPU 91%, threshold 90%.',
            'impact' => 'Device berpotensi lambat saat menjalankan Accurate 5.',
            'recommended_action' => 'Gunakan Remote Desktop untuk investigasi manual.',
            'status' => 'open',
            'dedupe_key' => hash('sha256', 'telegram-test'),
            'detected_at' => now(),
        ]);
    }
}
