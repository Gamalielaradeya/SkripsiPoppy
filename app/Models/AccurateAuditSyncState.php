<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccurateAuditSyncState extends Model
{
    protected $fillable = [
        'accurate_audit_source_id',
        'last_audit_id',
        'last_activity_time',
        'last_hash',
        'last_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'last_activity_time' => 'datetime',
            'last_synced_at' => 'datetime',
        ];
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(AccurateAuditSource::class, 'accurate_audit_source_id');
    }
}
