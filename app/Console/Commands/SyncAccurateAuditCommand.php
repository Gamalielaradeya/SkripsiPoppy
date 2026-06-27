<?php

namespace App\Console\Commands;

use App\Models\AccurateAuditSource;
use App\Services\AccurateAudit\AccurateAuditSyncService;
use Illuminate\Console\Command;

class SyncAccurateAuditCommand extends Command
{
    protected $signature = 'accurate:audit-sync
        {--source= : Accurate audit source id or name}
        {--limit= : Maximum audit rows to read}
        {--dry-run : Read and map rows without inserting or updating sync state}';

    protected $description = 'Sync Accurate Firebird AUDIT + USERS rows into monitoring audit events.';

    public function handle(AccurateAuditSyncService $syncService): int
    {
        $limit = (int) ($this->option('limit') ?: config('monitoring.accurate_audit.sync_limit', 100));
        if ($limit < 1) {
            $this->error('Sync limit must be greater than zero.');

            return self::FAILURE;
        }

        $limit = min($limit, 1000);
        $dryRun = (bool) $this->option('dry-run');
        $source = $this->resolveSource($dryRun);

        if (! $source) {
            $this->warn('Accurate audit source not configured. Set env placeholders and create/enable an audit source.');

            return self::SUCCESS;
        }

        if ($dryRun) {
            return $this->runDryRun($syncService, $source, $limit);
        }

        if (! config('monitoring.accurate_audit.enabled', false) && ! $this->option('source')) {
            $this->warn('Accurate audit sync is disabled.');

            return self::SUCCESS;
        }

        $summary = $syncService->sync($source, $limit);

        if ($summary['status'] === 'failed') {
            $this->error('Accurate audit sync failed safely: '.$summary['error_message']);

            return self::FAILURE;
        }

        $this->info(sprintf(
            'Accurate audit sync complete. run_id=%d rows=%d inserted=%d duplicates=%d',
            $summary['run_id'],
            $summary['rows_read'],
            $summary['inserted'],
            $summary['duplicates'],
        ));

        return self::SUCCESS;
    }

    private function runDryRun(AccurateAuditSyncService $syncService, AccurateAuditSource $source, int $limit): int
    {
        try {
            $summary = $syncService->dryRun($source, $limit);
        } catch (\Throwable $exception) {
            $this->error('Accurate audit dry-run failed safely: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->info(sprintf(
            'Accurate audit dry-run complete. rows=%d mapped=%d writes=0',
            $summary['rows_read'],
            $summary['mapped'],
        ));

        foreach ($summary['preview'] as $event) {
            $this->line(sprintf(
                'AUDITID=%s time=%s user=%s source=%s type=%s',
                $event['accurate_audit_id'] ?? '-',
                $event['activity_time']?->format('Y-m-d H:i:s') ?? '-',
                $event['accurate_username'] ?? '-',
                $event['source'] ?? '-',
                $event['transaction_type'] ?? '-',
            ));
        }

        return self::SUCCESS;
    }

    private function resolveSource(bool $dryRun): ?AccurateAuditSource
    {
        $sourceOption = $this->option('source');

        if ($sourceOption) {
            $query = AccurateAuditSource::query();

            if (ctype_digit((string) $sourceOption)) {
                $query->whereKey((int) $sourceOption);
            } else {
                $query->where('name', (string) $sourceOption);
            }

            return $query->first();
        }

        $source = AccurateAuditSource::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->first();

        if ($source) {
            return $source;
        }

        return $this->sourceFromConfig($dryRun);
    }

    private function sourceFromConfig(bool $dryRun): ?AccurateAuditSource
    {
        $config = config('monitoring.accurate_audit');

        if (empty($config['firebird_host']) || empty($config['firebird_database'])) {
            return null;
        }

        $attributes = [
            'name' => 'Default Accurate Firebird',
            'firebird_host' => $config['firebird_host'],
            'firebird_port' => (int) ($config['firebird_port'] ?: 3051),
            'database_path' => $config['firebird_database'],
            'username' => $config['firebird_username'] ?: null,
            'credential_ref' => 'ACCURATE_FIREBIRD_PASSWORD',
            'is_active' => true,
        ];

        if ($dryRun) {
            return new AccurateAuditSource($attributes);
        }

        return AccurateAuditSource::query()->create($attributes);
    }
}
