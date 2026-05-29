<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerServiceCheck extends Model
{
    protected $fillable = [
        'server_name',
        'service_name',
        'service_display_name',
        'service_status',
        'port',
        'port_status',
        'status',
        'raw_output',
        'checked_at',
    ];

    protected function casts(): array
    {
        return [
            'port' => 'integer',
            'checked_at' => 'datetime',
        ];
    }
}
