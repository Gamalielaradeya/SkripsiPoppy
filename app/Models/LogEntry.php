<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogEntry extends Model
{
    protected $table = 'logs';

    protected $fillable = [
        'device_id',
        'agent_id',
        'hostname',
        'ip_address',
        'facility',
        'source',
        'event_type',
        'category',
        'severity',
        'target_type',
        'target_id',
        'target_name',
        'raw_message',
        'parsed_message',
        'parsed_payload',
        'source_file',
        'logged_at',
        'hash',
    ];

    protected function casts(): array
    {
        return [
            'parsed_payload' => 'array',
            'logged_at' => 'datetime',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
