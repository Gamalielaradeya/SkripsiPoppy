<?php

namespace App\Http\Controllers;

use App\Models\ThresholdSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Label bahasa Indonesia untuk setiap key setting.
     */
    protected static function labels(): array
    {
        return [
            // Threshold — Device
            'heartbeat_warning_minutes' => 'Batas Peringatan Heartbeat',
            'heartbeat_critical_minutes' => 'Batas Kritis Heartbeat',
            // Threshold — Performance
            'cpu_warning_threshold' => 'Ambang Peringatan CPU (%)',
            'cpu_critical_threshold' => 'Ambang Kritis CPU (%)',
            'ram_warning_threshold' => 'Ambang Peringatan RAM (%)',
            'ram_critical_threshold' => 'Ambang Kritis RAM (%)',
            'disk_warning_threshold' => 'Ambang Peringatan Disk (%)',
            'disk_critical_threshold' => 'Ambang Kritis Disk (%)',
            // Threshold — Network
            'firebird_latency_warning_ms' => 'Batas Peringatan Latensi Firebird (ms)',
            'firebird_latency_critical_ms' => 'Batas Kritis Latensi Firebird (ms)',
            'firebird_port' => 'Port Firebird',
            // Threshold — Alerting
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
            'accurate_audit' => 'Audit Accurate & Firebird',
            'tindakan_jarak_jauh' => 'Tindakan Jarak Jauh',
            'keamanan' => 'Keamanan & Log',
        ];
    }

    /**
     * Build the structured env settings array from current .env values.
     * Format: [group_key => ['label' => ..., 'key' => ..., 'value' => ..., 'type' => ..., 'group' => ..., 'description' => ..., 'sensitive' => bool]]
     */
    protected static function envSettings(): array
    {
        return [
            'telegram_enabled' => [
                'label' => 'Notifikasi Telegram Aktif',
                'key' => 'TELEGRAM_ALERT_ENABLED',
                'value' => env('TELEGRAM_ALERT_ENABLED', 'false'),
                'type' => 'boolean',
                'group' => 'telegram',
                'description' => 'Aktifkan notifikasi alert melalui bot Telegram.',
                'sensitive' => false,
            ],
            'bot_token' => [
                'label' => 'Token Bot Telegram',
                'key' => 'TELEGRAM_BOT_TOKEN',
                'value' => env('TELEGRAM_BOT_TOKEN', ''),
                'type' => 'string',
                'group' => 'telegram',
                'description' => 'Token bot Telegram dari @BotFather.',
                'sensitive' => true,
            ],
            'chat_id' => [
                'label' => 'Chat ID Telegram',
                'key' => 'TELEGRAM_CHAT_ID',
                'value' => env('TELEGRAM_CHAT_ID', ''),
                'type' => 'string',
                'group' => 'telegram',
                'description' => 'Chat ID tujuan notifikasi (user/group/channel).',
                'sensitive' => true,
            ],
            'telegram_cooldown' => [
                'label' => 'Jeda Ulang Telegram (menit)',
                'key' => 'TELEGRAM_ALERT_COOLDOWN_MINUTES',
                'value' => env('TELEGRAM_ALERT_COOLDOWN_MINUTES', '2'),
                'type' => 'integer',
                'group' => 'telegram',
                'description' => 'Jeda minimal antar notifikasi Telegram yang sama.',
                'sensitive' => false,
            ],
            'audit_enabled' => [
                'label' => 'Sinkronisasi Audit Accurate Aktif',
                'key' => 'ACCURATE_AUDIT_ENABLED',
                'value' => env('ACCURATE_AUDIT_ENABLED', 'false'),
                'type' => 'boolean',
                'group' => 'accurate_audit',
                'description' => 'Aktifkan sinkronisasi audit dari Firebird.',
                'sensitive' => false,
            ],
            'firebird_host' => [
                'label' => 'Host Firebird',
                'key' => 'ACCURATE_FIREBIRD_HOST',
                'value' => env('ACCURATE_FIREBIRD_HOST', '127.0.0.1'),
                'type' => 'string',
                'group' => 'accurate_audit',
                'description' => 'Alamat IP/host server Firebird.',
                'sensitive' => false,
            ],
            'firebird_port' => [
                'label' => 'Port Firebird',
                'key' => 'ACCURATE_FIREBIRD_PORT',
                'value' => env('ACCURATE_FIREBIRD_PORT', '3051'),
                'type' => 'integer',
                'group' => 'accurate_audit',
                'description' => 'Port TCP Firebird (default: 3051).',
                'sensitive' => false,
            ],
            'firebird_database' => [
                'label' => 'Lokasi Database Firebird',
                'key' => 'ACCURATE_FIREBIRD_DATABASE',
                'value' => env('ACCURATE_FIREBIRD_DATABASE', '/firebird/data/XYZ.GDB'),
                'type' => 'string',
                'group' => 'accurate_audit',
                'description' => 'Path file .GDB Accurate di server Firebird.',
                'sensitive' => false,
            ],
            'firebird_username' => [
                'label' => 'Username Read-Only Firebird',
                'key' => 'ACCURATE_FIREBIRD_USERNAME',
                'value' => env('ACCURATE_FIREBIRD_USERNAME', 'GUEST'),
                'type' => 'string',
                'group' => 'accurate_audit',
                'description' => 'Username untuk koneksi read-only Firebird.',
                'sensitive' => false,
            ],
            'firebird_password' => [
                'label' => 'Password Read-Only Firebird',
                'key' => 'ACCURATE_FIREBIRD_PASSWORD',
                'value' => env('ACCURATE_FIREBIRD_PASSWORD', ''),
                'type' => 'string',
                'group' => 'accurate_audit',
                'description' => 'Password untuk koneksi read-only Firebird.',
                'sensitive' => true,
            ],
            'audit_sync_limit' => [
                'label' => 'Batas Sinkronisasi Audit',
                'key' => 'ACCURATE_AUDIT_SYNC_LIMIT',
                'value' => env('ACCURATE_AUDIT_SYNC_LIMIT', '100'),
                'type' => 'integer',
                'group' => 'accurate_audit',
                'description' => 'Jumlah maksimal event audit per sinkronisasi.',
                'sensitive' => false,
            ],
            'agent_api_enabled' => [
                'label' => 'Agent API Aktif',
                'key' => 'AGENT_API_ENABLED',
                'value' => env('AGENT_API_ENABLED', 'false'),
                'type' => 'boolean',
                'group' => 'tindakan_jarak_jauh',
                'description' => 'Aktifkan endpoint API untuk registrasi dan heartbeat agent.',
                'sensitive' => false,
            ],
            'remote_restart_enabled' => [
                'label' => 'Remote Restart Diizinkan',
                'key' => 'REMOTE_RESTART_ENABLED',
                'value' => env('REMOTE_RESTART_ENABLED', 'false'),
                'type' => 'boolean',
                'group' => 'tindakan_jarak_jauh',
                'description' => 'Izinkan admin mengirim perintah restart ke klien.',
                'sensitive' => false,
            ],
            'restart_require_reason' => [
                'label' => 'Restart Wajib Alasan',
                'key' => 'REMOTE_RESTART_REQUIRE_REASON',
                'value' => env('REMOTE_RESTART_REQUIRE_REASON', 'true'),
                'type' => 'boolean',
                'group' => 'tindakan_jarak_jauh',
                'description' => 'Restart klien wajib disertai alasan oleh admin.',
                'sensitive' => false,
            ],
            'command_expiry' => [
                'label' => 'Batas Kedaluwarsa Perintah (menit)',
                'key' => 'REMOTE_ACTION_COMMAND_EXPIRY_MINUTES',
                'value' => env('REMOTE_ACTION_COMMAND_EXPIRY_MINUTES', '10'),
                'type' => 'integer',
                'group' => 'tindakan_jarak_jauh',
                'description' => 'Batas waktu perintah menunggu agent sebelum expired.',
                'sensitive' => false,
            ],
            'restart_delay' => [
                'label' => 'Jeda Sebelum Restart (detik)',
                'key' => 'REMOTE_RESTART_DELAY_SECONDS',
                'value' => env('REMOTE_RESTART_DELAY_SECONDS', '30'),
                'type' => 'integer',
                'group' => 'tindakan_jarak_jauh',
                'description' => 'Waktu tunggu sebelum agent menjalankan restart.',
                'sensitive' => false,
            ],
        ];
    }

    /**
     * Perbarui satu baris di file .env.
     */
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

            // Skip comments and empty lines
            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                continue;
            }

            // Match KEY=value
            if (preg_match('/^' . preg_quote($key, '/') . '=/', $trimmed)) {
                // Preserve quoting style
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

            // Cari setting metadata untuk cek sensitive
            $meta = collect($settings)->first(fn ($s) => $s['key'] === $key);

            // Jangan overwrite password/token dengan nilai masked
            if ($meta && ($meta['sensitive'] ?? false) && str_contains($value, '*')) {
                continue;
            }

            static::setEnvValue($key, $value, $envPath);
        }

        // Clear config cache supaya nilai baru langsung dipakai
        \Illuminate\Support\Facades\Artisan::call('config:clear');

        return redirect()->route('settings.index')->with('status', 'Pengaturan berhasil diperbarui. Konfigurasi .env telah disimpan.');
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

            // Update ke monitoring.php config threshold yang ada DI .env:
            // threshold values don't live in .env currently, they're in config/monitoring.php hardcoded.
            // For now, we just save to DB. Future: bisa diintegrasikan.
        }

        return redirect()->route('settings.index')->with('status', 'Ambang pemantauan berhasil diperbarui.');
    }
}