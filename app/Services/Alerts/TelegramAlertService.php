<?php

namespace App\Services\Alerts;

use App\Models\Alert;
use App\Models\AlertNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TelegramAlertService
{
    public function sendContextualAlert(Alert $alert): AlertNotification
    {
        $message = $this->formatMessage($alert);

        if (! $this->isEnabled()) {
            return $this->record($alert, $message, 'skipped', null, 'Telegram alert is disabled.');
        }

        if ($this->cooldownActive($alert)) {
            return $this->record($alert, $message, 'skipped', null, 'Telegram notification cooldown active.');
        }

        $token = (string) config('monitoring.telegram.bot_token');
        $chatId = (string) config('monitoring.telegram.chat_id');

        if ($token === '' || $chatId === '') {
            return $this->record($alert, $message, 'failed', null, 'Telegram token or chat id is not configured.');
        }

        try {
            $response = Http::timeout(10)
                ->asForm()
                ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'disable_web_page_preview' => true,
                ]);
        } catch (\Throwable $exception) {
            return $this->record(
                $alert,
                $message,
                'failed',
                null,
                $this->safeError($exception->getMessage()),
            );
        }

        if ($response->successful() && $response->json('ok') !== false) {
            $result = $this->record($alert, $message, 'sent', now());

            // Prevent flooding Telegram with many alerts at once.
            usleep(800_000);

            return $result;
        }

        return $this->record(
            $alert,
            $message,
            'failed',
            null,
            'Telegram API returned HTTP '.$response->status().'.',
        );
    }

    public function formatMessage(Alert $alert): string
    {
        $alertUrl = route('alerts.show', $alert, absolute: true);

        return trim(sprintf(
            "[%s] %s\n\nTarget: %s\nDetected by: %s\nEvidence: %s\nImpact: %s\nRecommended action: %s\nTime: %s\nDashboard link: %s",
            strtoupper((string) $alert->severity),
            $alert->title,
            $alert->target_name,
            $alert->detected_by.($alert->source ? ' / '.$alert->source : ''),
            $alert->evidence_summary,
            $alert->impact,
            $alert->recommended_action,
            $alert->detected_at?->format('Y-m-d H:i:s') ?? now()->format('Y-m-d H:i:s'),
            $alertUrl,
        ));
    }

    private function isEnabled(): bool
    {
        return (bool) config('monitoring.telegram.enabled', false);
    }

    private function cooldownActive(Alert $alert): bool
    {
        $minutes = max(1, (int) config('monitoring.telegram.cooldown_minutes', 5));

        return $alert->notifications()
            ->where('channel', 'telegram')
            ->where('status', 'sent')
            ->where('sent_at', '>=', now()->subMinutes($minutes))
            ->exists();
    }

    private function record(
        Alert $alert,
        string $message,
        string $status,
        mixed $sentAt = null,
        ?string $errorMessage = null,
    ): AlertNotification {
        return $alert->notifications()->create([
            'channel' => 'telegram',
            'recipient' => $this->maskedRecipient(),
            'message' => $message,
            'status' => $status,
            'sent_at' => $sentAt,
            'error_message' => $errorMessage,
        ]);
    }

    private function maskedRecipient(): ?string
    {
        $chatId = (string) config('monitoring.telegram.chat_id');

        if ($chatId === '') {
            return null;
        }

        return (string) config('monitoring.telegram.recipient_name', 'Admin IT');
    }

    private function safeError(string $message): string
    {
        foreach ([
            (string) config('monitoring.telegram.bot_token'),
            (string) config('monitoring.telegram.chat_id'),
            (string) config('monitoring.accurate_audit.firebird_password'),
            (string) config('monitoring.accurate_audit.firebird_database'),
        ] as $secret) {
            if ($secret !== '') {
                $message = str_replace($secret, '[redacted]', $message);
            }
        }

        return Str::limit($message, 500, '');
    }
}
