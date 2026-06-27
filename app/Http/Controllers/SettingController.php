<?php

namespace App\Http\Controllers;

use App\Models\ThresholdSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

class SettingController extends Controller
{
    protected static function labels(): array
    {
        return [
            'heartbeat_warning_minutes' => 'Batas Peringatan Heartbeat',
            'heartbeat_critical_minutes' => 'Batas Kritis Heartbeat',
            'cpu_warning_threshold' => 'Ambang Peringatan CPU (%)',
            'cpu_critical_threshold' => 'Ambang Kritis CPU (%)',
            'ram_warning_threshold' => 'Ambang Peringatan RAM (%)',
            'ram_critical_threshold' => 'Ambang Kritis RAM (%)',
            'disk_warning_threshold' => 'Ambang Peringatan Disk (%)',
            'disk_critical_threshold' => 'Ambang Kritis Disk (%)',
            'firebird_latency_warning_ms' => 'Batas Peringatan Latensi Firebird (ms)',
            'firebird_latency_critical_ms' => 'Batas Kritis Latensi Firebird (ms)',
            'firebird_port' => 'Port Firebird',
            'alert_cooldown_minutes' => 'Jeda Ulang Alert (menit)',
            'telegram_cooldown_minutes' => 'Jeda Ulang Notifikasi Telegram (menit)',
        ];
    }

    protected static function groupLabels(): array
    {
        return [
            'device' => 'Pemantauan Perangkat',
            'performance' => 'Ambang Performa',
            'network' => 'Ambang Jaringan',
            'alerting' => 'Peringatan',
            'telegram' => 'Notifikasi Telegram',
            'accurate_audit' => 'Audit Accurate & Firebird',
            'tindakan_jarak_jauh' => 'Tindakan Jarak Jauh',
        ];
    }

    /**
     * Semua env-baked settings dibaca dari config() — bukan env().
     * Ini penting supaya tetap jalan meskipun config:cache menyala.
     */
    protected static function envSettings(): array
    {
        // factory function untuk baca config real-time (no stale cache)
        return \Closure::bind(function () {
            $cfg = fn (string $key, mixed $default = '') => data_get(config('monitoring'), $key, $default);

            return [
                'telegram_enabled' => [
                    'label' => 'Notifikasi Telegram Aktif',
                    'key' => 'TELEGRAM_ALERT_ENABLED',
                    'value' => $cfg('telegram.enabled', false) ? 'true' : 'false',
                    'type' => 'boolean',
                    'group' => 'telegram',
                    'description' => 'Aktifkan notifikasi alert melalui bot Telegram.',
                    'sensitive' => false,
                ],
                'bot_token' => [
                    'label' => 'Token Bot Telegram',
                    'key' => 'TELEGRAM_BOT_TOKEN',
                    'value' => (string) $cfg('telegram.bot_token', ''),
                    'type' => 'string',
                    'group' => 'telegram',
                    'description' => 'Token bot Telegram dari @BotFather.',
                    'sensitive' => true,
                ],
                'chat_id' => [
                    'label' => 'Chat ID Telegram',
                    'key' => 'TELEGRAM_CHAT_ID',
                    'value' => (string) $cfg('telegram.chat_id', ''),
                    'type' => 'string',
                    'group' => 'telegram',
                    'description' => 'Chat ID tujuan notifikasi (user/group/channel).',
                    'sensitive' => true,
                ],
                'telegram_cooldown' => [
                    'label' => 'Jeda Ulang Telegram (menit)',
                    'key' => 'TELEGRAM_ALERT_COOLDOWN_MINUTES',
                    'value' => (string) $cfg('telegram.cooldown_minutes', '2'),
                    'type' => 'integer',
                    'group' => 'telegram',
                    'description' => 'Jeda minimal antar notifikasi Telegram yang sama.',
                    'sensitive' => false,
                ],
                'audit_enabled' => [
                    'label' => 'Sinkronisasi Audit Accurate Aktif',
                    'key' => 'ACCURATE_AUDIT_ENABLED',
                    'value' => $cfg('accurate_audit.enabled', false) ? 'true' : 'false',
                    'type' => 'boolean',
                    'group' => 'accurate_audit',
                    'description' => 'Aktifkan sinkronisasi audit dari Firebird AUDIT + USERS.',
                    'sensitive' => false,
                ],
                'firebird_host' => [
                    'label' => 'Host Firebird',
                    'key' => 'ACCURATE_FIREBIRD_HOST',
                    'value' => (string) $cfg('accurate_audit.firebird_host', '127.0.0.1'),
                    'type' => 'string',
                    'group' => 'accurate_audit',
                    'description' => 'Alamat IP/host server Firebird.',
                    'sensitive' => false,
                ],
                'firebird_port' => [
                    'label' => 'Port Firebird',
                    'key' => 'ACCURATE_FIREBIRD_PORT',
                    'value' => (string) $cfg('accurate_audit.firebird_port', '3051'),
                    'type' => 'integer',
                    'group' => 'accurate_audit',
                    'description' => 'Port TCP Firebird (default: 3051).',
                    'sensitive' => false,
                ],
                'firebird_database' => [
                    'label' => 'Lokasi Database Firebird',
                    'key' => 'ACCURATE_FIREBIRD_DATABASE',
                    'value' => (string) $cfg('accurate_audit.firebird_database', '/firebird/data/XYZ.GDB'),
                    'type' => 'string',
                    'group' => 'accurate_audit',
                    'description' => 'Path file .GDB Accurate di server Firebird.',
                    'sensitive' => false,
                ],
                'firebird_username' => [
                    'label' => 'Username Read-Only Firebird',
                    'key' => 'ACCURATE_FIREBIRD_USERNAME',
                    'value' => (string) $cfg('accurate_audit.firebird_username', 'GUEST'),
                    'type' => 'string',
                    'group' => 'accurate_audit',
                    'description' => 'Username untuk koneksi read-only Firebird.',
                    'sensitive' => false,
                ],
                'firebird_password' => [
                    'label' => 'Password Read-Only Firebird',
                    'key' => 'ACCURATE_FIREBIRD_PASSWORD',
                    'value' => (string) $cfg('accurate_audit.firebird_password', ''),
                    'type' => 'string',
                    'group' => 'accurate_audit',
                    'description' => 'Password untuk koneksi read-only Firebird.',
                    'sensitive' => true,
                ],
                'audit_sync_limit' => [
                    'label' => 'Batas Sinkronisasi Audit',
                    'key' => 'ACCURATE_AUDIT_SYNC_LIMIT',
                    'value' => (string) $cfg('accurate_audit.sync_limit', '100'),
                    'type' => 'integer',
                    'group' => 'accurate_audit',
                    'description' => 'Jumlah maksimal event audit per sinkronisasi.',
                    'sensitive' => false,
                ],
                'agent_api_enabled' => [
                    'label' => 'Agent API Aktif',
                    'key' => 'AGENT_API_ENABLED',
                    'value' => $cfg('remote_action.agent_api_enabled', false) ? 'true' : 'false',
                    'type' => 'boolean',
                    'group' => 'tindakan_jarak_jauh',
                    'description' => 'Aktifkan endpoint API untuk registrasi dan heartbeat agent.',
                    'sensitive' => false,
                ],
                'remote_restart_enabled' => [
                    'label' => 'Remote Restart Diizinkan',
                    'key' => 'REMOTE_RESTART_ENABLED',
                    'value' => $cfg('remote_action.restart_enabled', false) ? 'true' : 'false',
                    'type' => 'boolean',
                    'group' => 'tindakan_jarak_jauh',
                    'description' => 'Izinkan admin mengirim perintah restart ke klien.',
                    'sensitive' => false,
                ],
                'restart_require_reason' => [
                    'label' => 'Restart Wajib Alasan',
                    'key' => 'REMOTE_RESTART_REQUIRE_REASON',
                    'value' => $cfg('remote_action.restart_require_reason', true) ? 'true' : 'false',
                    'type' => 'boolean',
                    'group' => 'tindakan_jarak_jauh',
                    'description' => 'Restart klien wajib disertai alasan oleh admin.',
                    'sensitive' => false,
                ],
                'command_expiry' => [
                    'label' => 'Batas Kedaluwarsa Perintah (menit)',
                    'key' => 'REMOTE_ACTION_COMMAND_EXPIRY_MINUTES',
                    'value' => (string) $cfg('remote_action.command_expiry_minutes', '10'),
                    'type' => 'integer',
                    'group' => 'tindakan_jarak_jauh',
                    'description' => 'Batas waktu perintah menunggu agent sebelum expired.',
                    'sensitive' => false,
                ],
                'restart_delay' => [
                    'label' => 'Jeda Sebelum Restart (detik)',
                    'key' => 'REMOTE_RESTART_DELAY_SECONDS',
                    'value' => (string) $cfg('remote_action.restart_delay_seconds', '30'),
                    'type' => 'integer',
                    'group' => 'tindakan_jarak_jauh',
                    'description' => 'Waktu tunggu sebelum agent menjalankan restart.',
                    'sensitive' => false,
                ],
            ];
        }, null)();
    }

    protected static function setEnvValue(string $key, string $value, string $envPath): void
    {
        if (! file_exists($envPath)) {
            return;
        }

        $content = file_get_contents($envPath);
        $lines = explode("\n", $content);
        $found = false;

        foreach ($lines as $i => $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                continue;
            }
            if (preg_match('/^' . preg_quote($key, '/') . '=/', $trimmed)) {
                $lines[$i] = $key . '=' . $value;
                $found = true;
                break;
            }
        }

        if (! $found) {
            $lines[] = $key . '=' . $value;
        }

        file_put_contents($envPath, implode("\n", $lines));
    }

    public function index(): View
    {
        $envSettings = [];
        foreach (static::envSettings() as $id => $s) {
            $group = $s['group'];
            if (! isset($envSettings[$group])) {
                $envSettings[$group] = [];
            }
            $envSettings[$group][] = array_merge($s, ['id' => $id]);
        }

        return view('settings.index', [
            'thresholdSettings' => ThresholdSetting::query()->orderBy('group')->orderBy('key')->get(),
            'envSettings' => $envSettings,
            'labels' => static::labels(),
            'groupLabels' => static::groupLabels(),
        ]);
    }

    public function updateEnv(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'env' => ['required', 'array'],
            'env.*.key' => ['required', 'string'],
            'env.*.value' => ['nullable', 'string'],
        ]);

        $envPath = base_path('.env');
        $settings = static::envSettings();

        foreach ($validated['env'] as $item) {
            $key = $item['key'];
            $value = $item['value'];

            $meta = collect($settings)->first(fn ($s) => $s['key'] === $key);

            // Jangan overwrite password/token dengan nilai masked
            if ($meta && ($meta['sensitive'] ?? false) && str_contains($value, '*')) {
                continue;
            }

            static::setEnvValue($key, $value, $envPath);
        }

        // Re-cache config supaya config('monitoring.xxx') baca nilai baru
        Artisan::call('config:cache');

        return redirect()->route('settings.index')->with('status', 'Pengaturan berhasil diperbarui. Konfigurasi .env telah disimpan dan config di-cache ulang.');
    }

    public function updateThresholds(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*.key' => ['required', 'string'],
            'settings.*.value' => ['required', 'string'],
        ]);

        foreach ($validated['settings'] as $item) {
            ThresholdSetting::query()
                ->where('key', $item['key'])
                ->where('is_editable', true)
                ->update(['value' => $item['value']]);
        }

        return redirect()->route('settings.index')->with('status', 'Ambang pemantauan berhasil diperbarui.');
    }
}