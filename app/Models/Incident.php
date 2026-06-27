<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Incident extends Model
{
    protected $fillable = [
        'device_id',
        'incident_code',
        'target_type',
        'target_id',
        'target_name',
        'severity',
        'title',
        'summary',
        'evidence_json',
        'status',
        'detected_at',
        'acknowledged_at',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'evidence_json' => 'array',
            'detected_at' => 'datetime',
            'acknowledged_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function alerts(): BelongsToMany
    {
        return $this->belongsToMany(Alert::class, 'incident_alerts')->withTimestamps();
    }
}
