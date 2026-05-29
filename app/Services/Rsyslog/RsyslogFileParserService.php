<?php

namespace App\Services\Rsyslog;

use App\Models\Device;
use App\Models\LogEntry;
use App\Models\ParserOffset;
use App\Models\ParserRun;
use App\Models\ThresholdSetting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RsyslogFileParserService
{
    public function __construct(
        private readonly StructuredSyslogLineParser $lineParser,
    ) {
    }

    public function parsePath(string $path, int $batchLimit = 500): array
    {
        $files = $this->resolveFiles($path);
        $summary = [
            'files' => count($files),
            'lines_scanned' => 0,
            'lines_parsed' => 0,
            'logs_created' => 0,
            'logs_skipped' => 0,
            'failed_files' => 0,
        ];

        foreach ($files as $file) {
            $fileSummary = $this->parseFile($file, $batchLimit);
            foreach (['lines_scanned', 'lines_parsed', 'logs_created', 'logs_skipped'] as $key) {
                $summary[$key] += $fileSummary[$key];
            }

            if ($fileSummary['status'] === 'failed') {
                $summary['failed_files']++;
            }
        }

        return $summary;
    }

    public function parseFile(string $sourceFile, int $batchLimit = 500): array
    {
        $sourceFile = realpath($sourceFile) ?: $sourceFile;
        $startedAt = now();

        $run = ParserRun::query()->create([
            'source_file' => $sourceFile,
            'status' => 'running',
            'started_at' => $startedAt,
        ]);

        $summary = [
            'source_file' => $sourceFile,
            'lines_scanned' => 0,
            'lines_parsed' => 0,
            'logs_created' => 0,
            'logs_skipped' => 0,
            'status' => 'success',
        ];

        try {
            $offset = ParserOffset::query()->firstOrCreate(
                ['source_file' => $sourceFile],
                ['last_position' => 0],
            );

            $file = new \SplFileObject($sourceFile, 'rb');
            $fileSize = filesize($sourceFile) ?: 0;
            $lastPosition = min((int) $offset->last_position, $fileSize);
            $file->fseek($lastPosition);
            $processed = 0;
            $lastLineHash = $offset->last_line_hash;

            while (! $file->eof() && $processed < $batchLimit) {
                $line = $file->fgets();
                if ($line === false || $line === '') {
                    break;
                }

                $processed++;
                $summary['lines_scanned']++;
                $parsed = $this->lineParser->parse($line);
                $currentPosition = $file->ftell();

                if (! $parsed) {
                    $summary['logs_skipped']++;
                    $lastPosition = $currentPosition;
                    continue;
                }

                $summary['lines_parsed']++;
                $hash = $this->hashLine($sourceFile, $parsed);
                $lastLineHash = $hash;

                if (LogEntry::query()->where('hash', $hash)->exists()) {
                    $summary['logs_skipped']++;
                } else {
                    LogEntry::query()->create($this->logEntryAttributes($sourceFile, $parsed, $hash));
                    $summary['logs_created']++;
                }

                $lastPosition = $currentPosition;
            }

            $offset->forceFill([
                'last_position' => $lastPosition,
                'last_line_hash' => $lastLineHash,
                'last_parsed_at' => now(),
            ])->save();
        } catch (\Throwable $exception) {
            $summary['status'] = 'failed';
            $run->forceFill([
                'lines_scanned' => $summary['lines_scanned'],
                'lines_parsed' => $summary['lines_parsed'],
                'logs_created' => $summary['logs_created'],
                'logs_skipped' => $summary['logs_skipped'],
                'status' => 'failed',
                'error_message' => Str::limit($exception->getMessage(), 1000, ''),
                'finished_at' => now(),
            ])->save();

            return $summary;
        }

        $run->forceFill([
            'lines_scanned' => $summary['lines_scanned'],
            'lines_parsed' => $summary['lines_parsed'],
            'logs_created' => $summary['logs_created'],
            'logs_skipped' => $summary['logs_skipped'],
            'status' => 'success',
            'finished_at' => now(),
        ])->save();

        return $summary;
    }

    private function resolveFiles(string $path): array
    {
        if (is_file($path)) {
            return [$path];
        }

        if (! is_dir($path)) {
            return [];
        }

        $allLog = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'all.log';
        if (is_file($allLog)) {
            return [$allLog];
        }

        $files = glob(rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'*.log') ?: [];
        sort($files);

        return array_values(array_filter($files, 'is_file'));
    }

    private function logEntryAttributes(string $sourceFile, array $parsed, string $hash): array
    {
        $payload = $parsed['parsed_payload'];
        $device = null;

        if (! empty($parsed['agent_id'])) {
            $device = Device::query()->where('agent_id', $parsed['agent_id'])->first();
        }

        [$category, $severity] = $this->classify($parsed['source'], $parsed['event_type'], $payload);

        return [
            'device_id' => $device?->id,
            'agent_id' => $parsed['agent_id'],
            'hostname' => $parsed['hostname'],
            'ip_address' => $parsed['ip_address'],
            'facility' => 'rsyslog',
            'source' => $parsed['source'],
            'event_type' => $parsed['event_type'],
            'category' => $category,
            'severity' => $severity,
            'target_type' => $device ? 'device' : null,
            'target_id' => $device ? (string) $device->id : null,
            'target_name' => $device?->display_name,
            'raw_message' => $parsed['raw_message'],
            'parsed_message' => $parsed['parsed_message'],
            'parsed_payload' => $payload + [
                '_syslog_hostname' => $parsed['syslog_hostname'],
                '_source' => $parsed['source'],
            ],
            'source_file' => $sourceFile,
            'logged_at' => $parsed['logged_at'] ?: Carbon::now(),
            'hash' => $hash,
        ];
    }

    private function classify(?string $source, ?string $eventType, array $payload): array
    {
        $category = match ($source) {
            'perf-monitor' => 'performance',
            'device-monitor', 'heartbeat-monitor' => 'device',
            default => match ($eventType) {
                'performance_status' => 'performance',
                'device_heartbeat', 'heartbeat_status' => 'device',
                default => 'unknown',
            },
        };

        $severity = 'info';

        if ($category === 'performance') {
            $severity = $this->performanceSeverity($payload);
        }

        return [$category, $severity];
    }

    private function performanceSeverity(array $payload): string
    {
        $thresholds = $this->performanceThresholds();
        $severity = 'info';

        foreach ([
            'cpu_usage_percent' => 'cpu',
            'ram_usage_percent' => 'ram',
            'disk_usage_percent' => 'disk',
        ] as $payloadKey => $prefix) {
            if (! isset($payload[$payloadKey]) || ! is_numeric($payload[$payloadKey])) {
                continue;
            }

            $value = (float) $payload[$payloadKey];
            if ($value >= $thresholds[$prefix.'_critical']) {
                return 'critical';
            }

            if ($value >= $thresholds[$prefix.'_warning']) {
                $severity = 'warning';
            }
        }

        return $severity;
    }

    private function performanceThresholds(): array
    {
        static $thresholds = null;

        if ($thresholds !== null) {
            return $thresholds;
        }

        $defaults = [
            'cpu_warning' => 80,
            'cpu_critical' => 90,
            'ram_warning' => 80,
            'ram_critical' => 90,
            'disk_warning' => 80,
            'disk_critical' => 90,
        ];

        $values = ThresholdSetting::query()
            ->whereIn('key', [
                'cpu_warning_threshold',
                'cpu_critical_threshold',
                'ram_warning_threshold',
                'ram_critical_threshold',
                'disk_warning_threshold',
                'disk_critical_threshold',
            ])
            ->pluck('value', 'key');

        return $thresholds = [
            'cpu_warning' => (float) ($values['cpu_warning_threshold'] ?? $defaults['cpu_warning']),
            'cpu_critical' => (float) ($values['cpu_critical_threshold'] ?? $defaults['cpu_critical']),
            'ram_warning' => (float) ($values['ram_warning_threshold'] ?? $defaults['ram_warning']),
            'ram_critical' => (float) ($values['ram_critical_threshold'] ?? $defaults['ram_critical']),
            'disk_warning' => (float) ($values['disk_warning_threshold'] ?? $defaults['disk_warning']),
            'disk_critical' => (float) ($values['disk_critical_threshold'] ?? $defaults['disk_critical']),
        ];
    }

    private function hashLine(string $sourceFile, array $parsed): string
    {
        $loggedAt = $parsed['logged_at']?->toIso8601String() ?? '';

        return hash('sha256', implode('|', [
            $sourceFile,
            $loggedAt,
            $parsed['hostname'] ?? '',
            $parsed['raw_message'],
        ]));
    }
}
