<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alert extends Model
{
    protected $fillable = [
        'device_id',
        'log_id',
        'accurate_audit_event_id',
        'alert_code',
        'target_type',
        'target_id',
        'target_name',
        'category',
        'severity',
        'title',
        'description',
        'detected_by',
        'source',
        'evidence_summary',
        'impact',
        'recommended_action',
        'status',
        'dedupe_key',
        'first_detected_at',
        'last_detected_at',
        'detected_at',
        'acknowledged_by',
        'acknowledged_at',
        'resolved_by',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'first_detected_at' => 'datetime',
            'last_detected_at' => 'datetime',
            'detected_at' => 'datetime',
            'acknowledged_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function log(): BelongsTo
    {
        return $this->belongsTo(LogEntry::class, 'log_id');
    }

    public function accurateAuditEvent(): BelongsTo
    {
        return $this->belongsTo(AccurateAuditEvent::class);
    }

    public function evidences(): HasMany
    {
        return $this->hasMany(AlertEvidence::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(AlertNotification::class);
    }

    public function incidents(): BelongsToMany
    {
        return $this->belongsToMany(Incident::class, 'incident_alerts')->withTimestamps();
    }
}
