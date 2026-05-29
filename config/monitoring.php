<?php

return [
    'rsyslog_remote_log_path' => env('RSYSLOG_REMOTE_LOG_PATH', '/var/log/remote'),
    'parser_batch_limit' => (int) env('PARSER_BATCH_LIMIT', 500),
];
