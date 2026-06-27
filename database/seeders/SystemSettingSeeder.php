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
        $settings = [
            ['group' => 'app', 'key' => 'app_display_name', 'value' => 'Centralized Log Monitoring Dashboard', 'data_type' => 'string', 'description' => 'Display name shown in the admin UI.', 'is_sensitive' => false],
            ['group' => 'app', 'key' => 'timezone', 'value' => 'Asia/Jakarta', 'data_type' => 'string', 'description' => 'Display timezone for operational timestamps.', 'is_sensitive' => false],
            ['group' => 'monitoring', 'key' => 'dashboard_refresh_seconds', 'value' => '30', 'data_type' => 'integer', 'description' => 'Dashboard refresh interval placeholder.', 'is_sensitive' => false],
            ['group' => 'accurate_audit', 'key' => 'audit_sync_limit', 'value' => '100', 'data_type' => 'integer', 'description' => 'Future Firebird audit reader batch limit.', 'is_sensitive' => false],
            ['group' => 'accurate_audit', 'key' => 'audit_sync_interval_seconds', 'value' => '60', 'data_type' => 'integer', 'description' => 'Future Firebird audit reader interval.', 'is_sensitive' => false],
            ['group' => 'accurate_audit', 'key' => 'firebird_host_env_key', 'value' => 'ACCURATE_FIREBIRD_HOST', 'data_type' => 'string', 'description' => 'Environment key name for Firebird host; no secret stored here.', 'is_sensitive' => true],
            ['group' => 'accurate_audit', 'key' => 'firebird_password_env_key', 'value' => 'ACCURATE_FIREBIRD_PASSWORD', 'data_type' => 'string', 'description' => 'Environment key name for Firebird password; no secret stored here.', 'is_sensitive' => true],
            ['group' => 'telegram', 'key' => 'telegram_enabled', 'value' => 'false', 'data_type' => 'boolean', 'description' => 'Telegram integration placeholder; implementation comes later.', 'is_sensitive' => false],
            ['group' => 'telegram', 'key' => 'telegram_bot_token_env_key', 'value' => 'TELEGRAM_BOT_TOKEN', 'data_type' => 'string', 'description' => 'Environment key name for Telegram bot token; no secret stored here.', 'is_sensitive' => true],
            ['group' => 'security', 'key' => 'raw_log_retention_days', 'value' => '30', 'data_type' => 'integer', 'description' => 'Future retention window for Advanced Logs data.', 'is_sensitive' => false],
            ['group' => 'remote_action', 'key' => 'restart_requires_reason', 'value' => 'true', 'data_type' => 'boolean', 'description' => 'Remote restart must remain manual, reasoned, and audited.', 'is_sensitive' => false],
            ['group' => 'remote_action', 'key' => 'command_expiry_minutes', 'value' => '10', 'data_type' => 'integer', 'description' => 'Short TTL for pending Windows Agent commands.', 'is_sensitive' => false],
        ];

        foreach ($settings as $setting) {
            SystemSetting::query()->updateOrCreate(
                ['key' => $setting['key']],
                $setting + ['is_editable' => true],
            );
        }
    }
}
