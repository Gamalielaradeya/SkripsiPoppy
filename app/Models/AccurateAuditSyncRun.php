<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccurateAuditSyncRun extends Model
{
    protected $fillable = [
        'accurate_audit_source_id',
        'total_rows_read',
        'total_inserted',
        'total_duplicates',
        'status',
        'error_message',
        'started_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'total_rows_read' => 'integer',
            'total_inserted' => 'integer',
            'total_duplicates' => 'integer',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(AccurateAuditSource::class, 'accurate_audit_source_id');
    }
}
