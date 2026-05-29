<?php

return [
    'rsyslog_remote_log_path' => env('RSYSLOG_REMOTE_LOG_PATH', '/var/log/remote'),
    'parser_batch_limit' => (int) env('PARSER_BATCH_LIMIT', 500),

    'accurate_audit' => [
        'enabled' => (bool) env('ACCURATE_AUDIT_ENABLED', false),
        'firebird_host' => env('ACCURATE_FIREBIRD_HOST'),
        'firebird_port' => (int) env('ACCURATE_FIREBIRD_PORT', 3051),
        'firebird_database' => env('ACCURATE_FIREBIRD_DATABASE'),
        'firebird_username' => env('ACCURATE_FIREBIRD_USERNAME'),
        'firebird_password' => env('ACCURATE_FIREBIRD_PASSWORD'),
        'firebird_charset' => env('ACCURATE_FIREBIRD_CHARSET', 'NONE'),
        'sync_limit' => (int) env('ACCURATE_AUDIT_SYNC_LIMIT', 100),
    ],
];
