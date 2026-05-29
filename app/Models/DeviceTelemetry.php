<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceTelemetry extends Model
{
    protected $fillable = [
        'device_id',
        'agent_id',
        'hostname',
        'windows_user',
        'ip_zerotier',
        'ip_local',
        'cpu_usage_percent',
        'ram_usage_percent',
        'disk_usage_percent',
        'uptime_seconds',
        'last_boot_at',
        'agent_status',
        'raw_payload',
        'reported_at',
    ];

    protected function casts(): array
    {
        return [
            'cpu_usage_percent' => 'float',
            'ram_usage_percent' => 'float',
            'disk_usage_percent' => 'float',
            'uptime_seconds' => 'integer',
            'last_boot_at' => 'datetime',
            'raw_payload' => 'array',
            'reported_at' => 'datetime',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
