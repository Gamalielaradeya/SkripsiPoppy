<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Device extends Model
{
    protected $fillable = [
        'agent_id',
        'hostname',
        'device_label',
        'description',
        'os_name',
        'os_version',
        'ip_zerotier',
        'ip_local',
        'mac_address',
        'windows_user',
        'agent_version',
        'agent_status',
        'rdp_status',
        'accurate_status',
        'firebird_connection_status',
        'status',
        'last_seen_at',
        'registered_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'last_seen_at' => 'datetime',
            'registered_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    protected function displayName(): Attribute
    {
        return Attribute::get(fn (): string => $this->device_label ?: $this->hostname);
    }

    public function agentCredentials(): HasMany
    {
        return $this->hasMany(AgentCredential::class);
    }

    public function telemetries(): HasMany
    {
        return $this->hasMany(DeviceTelemetry::class);
    }

    public function latestTelemetry(): HasOne
    {
        return $this->hasOne(DeviceTelemetry::class)->latestOfMany('reported_at');
    }

    public function networkChecks(): HasMany
    {
        return $this->hasMany(NetworkCheck::class);
    }

    public function accurateProcessSnapshots(): HasMany
    {
        return $this->hasMany(AccurateProcessSnapshot::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(LogEntry::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    public function remoteActions(): HasMany
    {
        return $this->hasMany(RemoteAction::class);
    }
}
