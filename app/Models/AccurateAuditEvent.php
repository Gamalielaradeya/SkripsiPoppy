<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccurateAuditEvent extends Model
{
    protected $fillable = [
        'accurate_audit_source_id',
        'accurate_audit_id',
        'accurate_user_id',
        'activity_time',
        'accurate_username',
        'accurate_fullname',
        'source',
        'transaction_type',
        'transaction_description',
        'invoice_no',
        'comp_name',
        'ip_address',
        'app_version',
        'status',
        'raw_payload',
        'hash',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'activity_time' => 'datetime',
            'raw_payload' => 'array',
            'synced_at' => 'datetime',
        ];
    }

    public function auditSource(): BelongsTo
    {
        return $this->belongsTo(AccurateAuditSource::class, 'accurate_audit_source_id');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }
}
