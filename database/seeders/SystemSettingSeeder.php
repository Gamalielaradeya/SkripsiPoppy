<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus env-key reference entries lama — ganti dengan nilai asli
        SystemSetting::query()->where('key', 'like', '%_env_key')->delete();

        $settings = [
            // ── Umum ──
            ['group' => 'umum', 'key' => 'app_display_name', 'value' => 'Centralized Log Monitoring Dashboard', 'data_type' => 'string', 'description' => 'Nama aplikasi yang ditampilkan di header.', 'is_sensitive' => false],
            ['group' => 'umum', 'key' => 'timezone', 'value' => 'Asia/Jakarta', 'data_type' => 'string', 'description' => 'Zona waktu operasional.', 'is_sensitive' => false],
            ['group' => 'umum', 'key' => 'dashboard_refresh_seconds', 'value' => '30', 'data_type' => 'integer', 'description' => 'Interval refresh otomatis dashboard (detik).', 'is_sensitive' => false],

            // ── Telegram ──
            ['group' => 'telegram', 'key' => 'telegram_enabled', 'value' => 'true', 'data_type' => 'boolean', 'description' => 'Aktifkan notifikasi alert melalui Telegram.', 'is_sensitive' => false],
            ['group' => 'telegram', 'key' => 'telegram_bot_token', 'value' => '0000000000:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', 'data_type' => 'string', 'description' => 'Token bot Telegram dari @BotFather.', 'is_sensitive' => true],
            ['group' => 'telegram', 'key' => 'telegram_chat_id', 'value' => '-1001234567890', 'data_type' => 'string', 'description' => 'Chat ID Telegram tujuan notifikasi (grup/channel).', 'is_sensitive' => true],
            ['group' => 'telegram', 'key' => 'telegram_cooldown_seconds', 'value' => '300', 'data_type' => 'integer', 'description' => 'Jeda minimal antar notifikasi Telegram yang sama (detik).', 'is_sensitive' => false],

            // ── Firebird / Accurate Audit ──
            ['group' => 'audit_accurate', 'key' => 'audit_sync_limit', 'value' => '100', 'data_type' => 'integer', 'description' => 'Jumlah maksimal event audit per sinkronisasi.', 'is_sensitive' => false],
            ['group' => 'audit_accurate', 'key' => 'audit_sync_interval_seconds', 'value' => '60', 'data_type' => 'integer', 'description' => 'Interval sinkronisasi audit Firebird (detik).', 'is_sensitive' => false],
            ['group' => 'audit_accurate', 'key' => 'firebird_host', 'value' => '10.147.17.1', 'data_type' => 'string', 'description' => 'Host IP Firebird server untuk koneksi read-only audit.', 'is_sensitive' => false],
            ['group' => 'audit_accurate', 'key' => 'firebird_database_path', 'value' => '/firebird/databases/XYZ.GDB', 'data_type' => 'string', 'description' => 'Path file database Accurate di server Firebird.', 'is_sensitive' => false],
            ['group' => 'audit_accurate', 'key' => 'firebird_readonly_username', 'value' => 'GUEST', 'data_type' => 'string', 'description' => 'Username read-only untuk akses Firebird.', 'is_sensitive' => false],
            ['group' => 'audit_accurate', 'key' => 'firebird_readonly_password', 'value' => 'guest', 'data_type' => 'string', 'description' => 'Password read-only untuk akses Firebird.', 'is_sensitive' => true],

            // ── Tindakan Jarak Jauh ──
            ['group' => 'tindakan_jarak_jauh', 'key' => 'restart_requires_reason', 'value' => 'true', 'data_type' => 'boolean', 'description' => 'Restart klien wajib disertai alasan oleh admin.', 'is_sensitive' => false],
            ['group' => 'tindakan_jarak_jauh', 'key' => 'command_expiry_minutes', 'value' => '10', 'data_type' => 'integer', 'description' => 'Batas waktu kedaluwarsa perintah menunggu agent (menit).', 'is_sensitive' => false],

            // ── Keamanan & Log ──
            ['group' => 'keamanan', 'key' => 'raw_log_retention_days', 'value' => '30', 'data_type' => 'integer', 'description' => 'Lama penyimpanan log mentah sebelum dihapus (hari).', 'is_sensitive' => false],

            // ── ZeroTier ──
            ['group' => 'zerotier', 'key' => 'zerotier_network_id', 'value' => 'e4da7455b2b688af', 'data_type' => 'string', 'description' => 'Network ID ZeroTier untuk jaringan privat Poppy.', 'is_sensitive' => false],
            ['group' => 'zerotier', 'key' => 'zerotier_vps_ip', 'value' => '10.147.17.1', 'data_type' => 'string', 'description' => 'IP ZeroTier VPS monitoring.', 'is_sensitive' => false],
        ];

        foreach ($settings as $setting) {
            SystemSetting::query()->updateOrCreate(
                ['key' => $setting['key']],
                $setting + ['is_editable' => true],
            );
        }
    }
}