<?php

namespace App\Services\AccurateAudit;

use App\Models\AccurateAuditEvent;
use App\Models\AccurateAuditSource;
use App\Models\AccurateAuditSyncRun;
use App\Models\AccurateAuditSyncState;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccurateAuditSyncService
{
    public function __construct(
        private readonly AccurateAuditReaderInterface $reader,
        private readonly AccurateAuditMapper $mapper,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function sync(AccurateAuditSource $source, int $limit): array
    {
        $startedAt = now();
        $run = AccurateAuditSyncRun::query()->create([
            'accurate_audit_source_id' => $source->id,
            'status' => 'running',
            'started_at' => $startedAt,
        ]);

        try {
            $state = AccurateAuditSyncState::query()->firstOrCreate([
                'accurate_audit_source_id' => $source->id,
            ]);

            $rows = $this->reader->fetchAuditRows($source, $state, $limit);
            $mappedRows = array_map(fn (array $row): array => $this->mapper->mapRow($row), $rows);
            $summary = $this->persistMappedRows($source, $state, $mappedRows);

            $run->forceFill([
                'total_rows_read' => count($rows),
                'total_inserted' => $summary['inserted'],
                'total_duplicates' => $summary['duplicates'],
                'status' => 'success',
                'finished_at' => now(),
            ])->save();

            $source->forceFill([
                'last_connection_status' => 'connected',
                'last_connection_error' => null,
                'last_connected_at' => now(),
            ])->save();

            return [
                'status' => 'success',
                'rows_read' => count($rows),
                'inserted' => $summary['inserted'],
                'duplicates' => $summary['duplicates'],
                'run_id' => $run->id,
            ];
        } catch (\Throwable $exception) {
            $message = $this->safeErrorMessage($exception, $source);

            $run->forceFill([
                'status' => 'failed',
                'error_message' => $message,
                'finished_at' => now(),
            ])->save();

            $source->forceFill([
                'last_connection_status' => 'failed',
                'last_connection_error' => $message,
            ])->save();

            return [
                'status' => 'failed',
                'rows_read' => 0,
                'inserted' => 0,
                'duplicates' => 0,
                'run_id' => $run->id,
                'error_message' => $message,
            ];
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function dryRun(AccurateAuditSource $source, int $limit): array
    {
        $state = $source->exists ? $source->syncState : null;
        $rows = $this->reader->fetchAuditRows($source, $state, $limit);
        $mappedRows = array_map(fn (array $row): array => $this->mapper->mapRow($row), $rows);

        return [
            'status' => 'dry-run',
            'rows_read' => count($rows),
            'mapped' => count($mappedRows),
            'preview' => array_slice($mappedRows, 0, 5),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $mappedRows
     * @return array{inserted: int, duplicates: int}
     */
    private function persistMappedRows(AccurateAuditSource $source, AccurateAuditSyncState $state, array $mappedRows): array
    {
        return DB::transaction(function () use ($source, $state, $mappedRows): array {
            $inserted = 0;
            $duplicates = 0;
            $lastRow = null;

            foreach ($mappedRows as $mapped) {
                $lastRow = $mapped;

                $exists = AccurateAuditEvent::query()
                    ->where('accurate_audit_source_id', $source->id)
                    ->where(function ($query) use ($mapped): void {
                        $query->where('accurate_audit_id', $mapped['accurate_audit_id'])
                            ->orWhere('hash', $mapped['hash']);
                    })
                    ->exists();

                if ($exists) {
                    $duplicates++;

                    continue;
                }

                AccurateAuditEvent::query()->create($mapped + [
                    'accurate_audit_source_id' => $source->id,
                ]);
                $inserted++;
            }

            if ($lastRow !== null) {
                $state->forceFill([
                    'last_audit_id' => $lastRow['accurate_audit_id'],
                    'last_activity_time' => $lastRow['activity_time'],
                    'last_hash' => $lastRow['hash'],
                    'last_synced_at' => now(),
                ])->save();
            } else {
                $state->forceFill([
                    'last_synced_at' => now(),
                ])->save();
            }

            return ['inserted' => $inserted, 'duplicates' => $duplicates];
        });
    }

    private function safeErrorMessage(\Throwable $exception, AccurateAuditSource $source): string
    {
        $message = $exception instanceof AccurateAuditReaderException
            ? $exception->getMessage()
            : 'Accurate audit sync failed safely.';

        foreach ([
            $source->firebird_host,
            $source->database_path,
            $source->username,
            (string) config('monitoring.accurate_audit.firebird_password', ''),
        ] as $sensitiveValue) {
            if ($sensitiveValue) {
                $message = str_replace((string) $sensitiveValue, '[redacted]', $message);
            }
        }

        return Str::limit($message, 1000, '');
    }
}
