<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class MonitoringHealthCommand extends Command
{
    protected $signature = 'monitoring:health';

    protected $description = 'Run read-only deployment health checks without exposing secrets.';

    /**
     * @var array<int, string>
     */
    private array $statuses = [];

    public function handle(): int
    {
        $this->info('Centralized Log Monitoring Dashboard health check');

        $this->checkApplication();
        $this->checkDatabase();
        $this->checkMonitoringPaths();
        $this->checkCommands();
        $this->checkFirebirdDriver();
        $this->checkTelegramConfig();
        $this->checkSchedulerGuidance();

        if (in_array('FAIL', $this->statuses, true)) {
            $this->newLine();
            $this->error('Health check finished with FAIL items.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->info(in_array('WARN', $this->statuses, true)
            ? 'Health check finished with WARN items.'
            : 'Health check finished: all checked items PASS.');

        return self::SUCCESS;
    }

    private function checkApplication(): void
    {
        $this->line('Application');
        $this->report('PASS', 'Laravel booted', sprintf(
            'env=%s debug=%s url=%s',
            app()->environment(),
            config('app.debug') ? 'true' : 'false',
            config('app.url') ?: 'not-set',
        ));
    }

    private function checkDatabase(): void
    {
        $this->line('Database');

        try {
            DB::connection()->select('select 1');
            $this->report('PASS', 'Database connection available', 'connection='.config('database.default'));
        } catch (\Throwable $exception) {
            $this->report('FAIL', 'Database connection failed', $this->safeMessage($exception->getMessage()));
        }
    }

    private function checkMonitoringPaths(): void
    {
        $this->line('Monitoring paths');

        $path = (string) config('monitoring.rsyslog_remote_log_path', '/var/log/remote');
        $limit = (int) config('monitoring.parser_batch_limit', 500);

        if ($path === '') {
            $this->report('WARN', 'RSyslog path not configured', 'Set RSYSLOG_REMOTE_LOG_PATH for VPS parser runs.');
        } elseif (! file_exists($path)) {
            $this->report('WARN', 'RSyslog path missing', $path);
        } elseif (! is_readable($path)) {
            $this->report('FAIL', 'RSyslog path is not readable', $path);
        } else {
            $type = is_dir($path) ? 'directory' : 'file';
            $this->report('PASS', 'RSyslog path readable', "{$type}: {$path}");
        }

        $this->report($limit > 0 ? 'PASS' : 'FAIL', 'Parser batch limit', (string) $limit);
    }

    private function checkCommands(): void
    {
        $this->line('Artisan commands');

        $commands = Artisan::all();
        foreach (['rsyslog:parse', 'accurate:audit-sync', 'alerts:detect', 'monitoring:health'] as $command) {
            $this->report(
                array_key_exists($command, $commands) ? 'PASS' : 'FAIL',
                "Command available: {$command}",
            );
        }
    }

    private function checkFirebirdDriver(): void
    {
        $this->line('Firebird');

        $this->report(
            extension_loaded('pdo_firebird') ? 'PASS' : 'WARN',
            'pdo_firebird extension',
            extension_loaded('pdo_firebird')
                ? 'installed'
                : 'not installed; required only when Accurate audit sync runs on this server',
        );
    }

    private function checkTelegramConfig(): void
    {
        $this->line('Telegram');

        $enabled = (bool) config('monitoring.telegram.enabled', false);
        if (! $enabled) {
            $this->report('WARN', 'Telegram alerting disabled', 'Set TELEGRAM_ALERT_ENABLED=true for real-device demo.');

            return;
        }

        $hasToken = filled(config('monitoring.telegram.bot_token'));
        $hasChat = filled(config('monitoring.telegram.chat_id'));

        $this->report($hasToken ? 'PASS' : 'FAIL', 'Telegram bot token configured', $hasToken ? 'present' : 'missing');
        $this->report($hasChat ? 'PASS' : 'FAIL', 'Telegram chat id configured', $hasChat ? 'present' : 'missing');
    }

    private function checkSchedulerGuidance(): void
    {
        $this->line('Scheduler');

        $events = app(Schedule::class)->events();
        if ($events === []) {
            $this->report(
                'WARN',
                'No Laravel scheduler entries registered',
                'Configure cron: * * * * * php artisan schedule:run, or run rsyslog/audit/alert commands manually during UAT.',
            );

            return;
        }

        $this->report('PASS', 'Laravel scheduler entries registered', count($events).' event(s)');
    }

    private function report(string $status, string $label, ?string $detail = null): void
    {
        $this->statuses[] = $status;
        $line = sprintf('[%s] %s', $status, $label);

        if ($detail !== null && $detail !== '') {
            $line .= ' - '.$detail;
        }

        match ($status) {
            'PASS' => $this->line("<info>{$line}</info>"),
            'WARN' => $this->line("<comment>{$line}</comment>"),
            default => $this->line("<error>{$line}</error>"),
        };
    }

    private function safeMessage(string $message): string
    {
        $sensitiveMarkers = [
            config('monitoring.telegram.bot_token'),
            config('monitoring.telegram.chat_id'),
            config('monitoring.accurate_audit.firebird_password'),
            env('DB_PASSWORD'),
        ];

        foreach ($sensitiveMarkers as $marker) {
            if (is_string($marker) && $marker !== '') {
                $message = str_replace($marker, '[redacted]', $message);
            }
        }

        return $message;
    }
}
