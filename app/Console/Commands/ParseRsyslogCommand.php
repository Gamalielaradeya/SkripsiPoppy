<?php

namespace App\Console\Commands;

use App\Services\Rsyslog\RsyslogFileParserService;
use Illuminate\Console\Command;

class ParseRsyslogCommand extends Command
{
    protected $signature = 'rsyslog:parse
        {--path= : Override RSYSLOG_REMOTE_LOG_PATH for one run}
        {--limit= : Maximum new lines to scan per file}';

    protected $description = 'Parse structured RSyslog remote log files into Advanced Logs.';

    public function handle(RsyslogFileParserService $parser): int
    {
        $path = $this->option('path') ?: config('monitoring.rsyslog_remote_log_path', '/var/log/remote');
        $limit = (int) ($this->option('limit') ?: config('monitoring.parser_batch_limit', 500));

        if ($limit < 1) {
            $this->error('Parser limit must be greater than zero.');

            return self::FAILURE;
        }

        if (! file_exists($path)) {
            $this->warn("RSyslog path not found: {$path}");

            return self::SUCCESS;
        }

        $summary = $parser->parsePath($path, $limit);

        $this->info(sprintf(
            'RSyslog parse complete. files=%d scanned=%d parsed=%d created=%d skipped=%d failed_files=%d',
            $summary['files'],
            $summary['lines_scanned'],
            $summary['lines_parsed'],
            $summary['logs_created'],
            $summary['logs_skipped'],
            $summary['failed_files'],
        ));

        return $summary['failed_files'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
