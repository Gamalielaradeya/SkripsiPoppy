<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RemoteAction extends Model
{
    public const ACTION_OPEN_RDP = 'OPEN_RDP';
    public const ACTION_RESTART_CLIENT = 'RESTART_CLIENT';
    public const ACTION_PING_TEST = 'PING_TEST';
    public const ACTION_RESTART_AGENT = 'RESTART_AGENT';

    protected $fillable = [
        'device_id',
        'requested_by',
        'action_type',
        'status',
        'reason',
        'requires_confirmation',
        'confirmed_by',
        'confirmed_at',
        'payload',
        'result_message',
        'error_message',
        'requested_at',
        'expires_at',
        'picked_up_at',
        'executed_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'requires_confirmation' => 'boolean',
            'confirmed_at' => 'datetime',
            'payload' => 'array',
            'requested_at' => 'datetime',
            'expires_at' => 'datetime',
            'picked_up_at' => 'datetime',
            'executed_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}
