<?php

return [
    'rsyslog_remote_log_path' => env('RSYSLOG_REMOTE_LOG_PATH', '/var/log/remote'),
    'parser_batch_limit' => (int) env('PARSER_BATCH_LIMIT', 500),

    'alerting' => [
        'heartbeat_warning_minutes' => 5,
        'heartbeat_critical_minutes' => 15,
        'cpu_warning_threshold' => 80,
        'cpu_critical_threshold' => 90,
        'ram_warning_threshold' => 80,
        'disk_warning_threshold' => 80,
        'firebird_latency_warning_ms' => 500,
        'firebird_port' => 3051,
        'alert_cooldown_minutes' => 5,
        'accurate_process_check_enabled' => true,
    ],

    'telegram' => [
        'enabled' => (bool) env('TELEGRAM_ALERT_ENABLED', false),
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'chat_id' => env('TELEGRAM_CHAT_ID'),
        'cooldown_minutes' => (int) env('TELEGRAM_ALERT_COOLDOWN_MINUTES', 5),
    ],

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

    'remote_action' => [
        'command_expiry_minutes' => (int) env('REMOTE_ACTION_COMMAND_EXPIRY_MINUTES', 10),
        'restart_delay_seconds' => (int) env('REMOTE_RESTART_DELAY_SECONDS', 30),
    ],
];
