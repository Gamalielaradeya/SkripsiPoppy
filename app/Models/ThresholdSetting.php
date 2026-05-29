<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThresholdSetting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value',
        'data_type',
        'description',
        'is_editable',
    ];

    protected function casts(): array
    {
        return [
            'is_editable' => 'boolean',
        ];
    }
}
