<?php

namespace Database\Seeders;

use App\Models\ThresholdSetting;
use Illuminate\Database\Seeder;

class ThresholdSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['group' => 'device', 'key' => 'heartbeat_warning_minutes', 'value' => '5', 'data_type' => 'integer', 'description' => 'Warning when a device has not sent heartbeat within this window.'],
            ['group' => 'device', 'key' => 'heartbeat_critical_minutes', 'value' => '15', 'data_type' => 'integer', 'description' => 'Critical/offline when a device has not sent heartbeat within this window.'],
            ['group' => 'performance', 'key' => 'cpu_warning_threshold', 'value' => '80', 'data_type' => 'integer', 'description' => 'CPU usage warning threshold percentage.'],
            ['group' => 'performance', 'key' => 'cpu_critical_threshold', 'value' => '90', 'data_type' => 'integer', 'description' => 'CPU usage critical threshold percentage.'],
            ['group' => 'performance', 'key' => 'ram_warning_threshold', 'value' => '80', 'data_type' => 'integer', 'description' => 'RAM usage warning threshold percentage.'],
            ['group' => 'performance', 'key' => 'ram_critical_threshold', 'value' => '90', 'data_type' => 'integer', 'description' => 'RAM usage critical threshold percentage.'],
            ['group' => 'performance', 'key' => 'disk_warning_threshold', 'value' => '80', 'data_type' => 'integer', 'description' => 'Disk usage warning threshold percentage.'],
            ['group' => 'performance', 'key' => 'disk_critical_threshold', 'value' => '90', 'data_type' => 'integer', 'description' => 'Disk usage critical threshold percentage.'],
            ['group' => 'network', 'key' => 'firebird_latency_warning_ms', 'value' => '500', 'data_type' => 'integer', 'description' => 'Firebird TCP latency warning threshold in milliseconds.'],
            ['group' => 'network', 'key' => 'firebird_latency_critical_ms', 'value' => '1500', 'data_type' => 'integer', 'description' => 'Firebird TCP latency critical threshold in milliseconds.'],
            ['group' => 'network', 'key' => 'firebird_port', 'value' => '3051', 'data_type' => 'integer', 'description' => 'Accurate Firebird service port.'],
            ['group' => 'alerting', 'key' => 'alert_cooldown_minutes', 'value' => '5', 'data_type' => 'integer', 'description' => 'Minimum delay before repeating the same contextual alert.'],
            ['group' => 'alerting', 'key' => 'telegram_cooldown_minutes', 'value' => '5', 'data_type' => 'integer', 'description' => 'Minimum delay before repeating the same Telegram notification.'],
        ];

        foreach ($settings as $setting) {
            ThresholdSetting::query()->updateOrCreate(
                ['key' => $setting['key']],
                $setting + ['is_editable' => true],
            );
        }
    }
}
