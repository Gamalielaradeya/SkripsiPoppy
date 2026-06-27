<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NetworkCheck extends Model
{
    protected $fillable = [
        'device_id',
        'target_type',
        'target_name',
        'target_host',
        'target_port',
        'ping_status',
        'ping_latency_ms',
        'tcp_status',
        'tcp_latency_ms',
        'packet_loss_percent',
        'status',
        'raw_payload',
        'checked_at',
    ];

    protected function casts(): array
    {
        return [
            'target_port' => 'integer',
            'ping_latency_ms' => 'float',
            'tcp_latency_ms' => 'float',
            'packet_loss_percent' => 'float',
            'raw_payload' => 'array',
            'checked_at' => 'datetime',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
