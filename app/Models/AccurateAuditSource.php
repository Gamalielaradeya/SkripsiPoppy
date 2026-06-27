<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AccurateAuditSource extends Model
{
    protected $fillable = [
        'name',
        'firebird_host',
        'firebird_port',
        'database_path',
        'username',
        'credential_ref',
        'is_active',
        'last_connection_status',
        'last_connection_error',
        'last_connected_at',
    ];

    protected function casts(): array
    {
        return [
            'firebird_port' => 'integer',
            'is_active' => 'boolean',
            'last_connected_at' => 'datetime',
        ];
    }

    public function events(): HasMany
    {
        return $this->hasMany(AccurateAuditEvent::class);
    }

    public function syncState(): HasOne
    {
        return $this->hasOne(AccurateAuditSyncState::class);
    }

    public function syncRuns(): HasMany
    {
        return $this->hasMany(AccurateAuditSyncRun::class);
    }
}
