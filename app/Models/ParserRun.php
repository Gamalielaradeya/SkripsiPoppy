<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParserRun extends Model
{
    protected $fillable = [
        'source_file',
        'lines_scanned',
        'lines_parsed',
        'logs_created',
        'logs_skipped',
        'status',
        'error_message',
        'started_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'lines_scanned' => 'integer',
            'lines_parsed' => 'integer',
            'logs_created' => 'integer',
            'logs_skipped' => 'integer',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }
}
