<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertEvidence extends Model
{
    protected $table = 'alert_evidences';

    protected $fillable = [
        'alert_id',
        'evidence_key',
        'evidence_value',
        'evidence_type',
        'source',
        'measured_at',
    ];

    protected function casts(): array
    {
        return [
            'measured_at' => 'datetime',
        ];
    }

    public function alert(): BelongsTo
    {
        return $this->belongsTo(Alert::class);
    }
}
