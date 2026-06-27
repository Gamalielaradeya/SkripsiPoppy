<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value',
        'data_type',
        'description',
        'is_sensitive',
        'is_editable',
    ];

    protected function casts(): array
    {
        return [
            'is_sensitive' => 'boolean',
            'is_editable' => 'boolean',
        ];
    }
}
