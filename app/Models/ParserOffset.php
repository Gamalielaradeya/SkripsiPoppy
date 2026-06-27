<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParserOffset extends Model
{
    protected $fillable = [
        'source_file',
        'last_position',
        'last_line_hash',
        'last_parsed_at',
    ];

    protected function casts(): array
    {
        return [
            'last_position' => 'integer',
            'last_parsed_at' => 'datetime',
        ];
    }
}
