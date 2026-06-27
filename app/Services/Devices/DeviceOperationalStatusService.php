<?php

namespace App\Services\Devices;

use App\Models\Device;
use App\Models\ThresholdSetting;
use Illuminate\Support\Facades\Schema;

class DeviceOperationalStatusService
{
    /**
     * @var array{warning: int, critical: int}|null
     */
    private ?array $thresholds = null;

    public function for(Device $device): string
    {
        if (! $device->last_seen_at) {
            return 'unknown';
        }

        $thresholds = $this->thresholds();

        if ($device->last_seen_at->lt(now()->subMinutes($thresholds['critical']))) {
            return 'offline';
        }

        if ($device->last_seen_at->lt(now()->subMinutes($thresholds['warning']))) {
            return 'warning';
        }

        return 'online';
    }

    /**
     * @return array{warning: int, critical: int}
     */
    public function thresholds(): array
    {
        if ($this->thresholds !== null) {
            return $this->thresholds;
        }

        $warning = $this->fallback('heartbeat_warning_minutes', 5);
        $critical = $this->fallback('heartbeat_critical_minutes', 15);

        if (Schema::hasTable('threshold_settings')) {
            $stored = ThresholdSetting::query()
                ->whereIn('key', ['heartbeat_warning_minutes', 'heartbeat_critical_minutes'])
                ->pluck('value', 'key');

            $warning = $this->positiveInteger($stored->get('heartbeat_warning_minutes'), $warning);
            $critical = $this->positiveInteger($stored->get('heartbeat_critical_minutes'), $critical);
        }

        return $this->thresholds = [
            'warning' => $warning,
            'critical' => max($warning + 1, $critical),
        ];
    }

    private function fallback(string $key, int $default): int
    {
        return $this->positiveInteger(config("monitoring.alerting.{$key}"), $default);
    }

    private function positiveInteger(mixed $value, int $fallback): int
    {
        return is_numeric($value) && (int) $value > 0
            ? (int) $value
            : $fallback;
    }
}
