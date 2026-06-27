<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccurateProcessSnapshot extends Model
{
    protected $fillable = [
        'device_id',
        'process_name',
        'process_status',
        'process_pid',
        'process_owner',
        'process_path',
        'process_started_at',
        'raw_payload',
        'checked_at',
    ];

    protected function casts(): array
    {
        return [
            'process_pid' => 'integer',
            'process_started_at' => 'datetime',
            'raw_payload' => 'array',
            'checked_at' => 'datetime',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
