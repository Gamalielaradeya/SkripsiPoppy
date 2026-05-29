<?php

namespace Tests\Feature;

use App\Models\AccurateAuditSource;
use App\Models\AccurateAuditSyncState;
use App\Services\AccurateAudit\AccurateAuditMapper;
use App\Services\AccurateAudit\AccurateAuditReaderException;
use App\Services\AccurateAudit\AccurateAuditReaderInterface;
use App\Services\AccurateAudit\AccurateAuditSyncService;
use App\Services\AccurateAudit\PdoFirebirdAccurateAuditReader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AccurateAuditSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_mapper_maps_confirmed_fields_and_accepts_nullable_poc_fields(): void
    {
        $mapped = app(AccurateAuditMapper::class)->mapRow($this->auditRow([
            'COMP_NAME' => null,
            'IPADDRESS' => '',
            'UNKNOWN_COLUMN' => 'preserved',
        ]));

        $this->assertSame('1001', $mapped['accurate_audit_id']);
        $this->assertSame('42', $mapped['accurate_user_id']);
        $this->assertSame('FINANCE01', $mapped['accurate_username']);
        $this->assertNull($mapped['comp_name']);
        $this->assertNull($mapped['ip_address']);
        $this->assertSame('preserved', $mapped['raw_payload']['UNKNOWN_COLUMN']);
        $this->assertNotEmpty($mapped['hash']);
    }

    public function test_sync_inserts_events_deduplicates_and_updates_state(): void
    {
        $source = $this->source();
        $this->fakeReader([$this->auditRow()]);
        $service = app(AccurateAuditSyncService::class);

        $first = $service->sync($source, 100);
        $second = $service->sync($source, 100);

        $this->assertSame('success', $first['status']);
        $this->assertSame(1, $first['inserted']);
        $this->assertSame(0, $first['duplicates']);
        $this->assertSame(0, $second['inserted']);
        $this->assertSame(1, $second['duplicates']);
        $this->assertDatabaseCount('accurate_audit_events', 1);

        $state = AccurateAuditSyncState::query()->firstOrFail();
        $this->assertSame('1001', $state->last_audit_id);
        $this->assertNotNull($state->last_activity_time);
        $this->assertNotNull($state->last_synced_at);
    }

    public function test_dry_run_maps_rows_without_inserting_or_updating_state(): void
    {
        $source = $this->source();
        $this->fakeReader([$this->auditRow()]);

        $summary = app(AccurateAuditSyncService::class)->dryRun($source, 100);

        $this->assertSame('dry-run', $summary['status']);
        $this->assertSame(1, $summary['mapped']);
        $this->assertDatabaseCount('accurate_audit_events', 0);
        $this->assertDatabaseCount('accurate_audit_sync_states', 0);
        $this->assertDatabaseCount('accurate_audit_sync_runs', 0);
    }

    public function test_command_records_failed_sync_run_with_safe_message(): void
    {
        $source = $this->source([
            'firebird_host' => '10.10.10.5',
            'database_path' => '/secret/path/accurate.fdb',
            'username' => 'SECRET_USER',
        ]);

        config()->set('monitoring.accurate_audit.enabled', true);
        config()->set('monitoring.accurate_audit.firebird_password', 'SECRET_PASSWORD');
        $this->fakeReader([], new AccurateAuditReaderException('Firebird PDO driver is not installed. Enable pdo_firebird on the server.'));

        $exitCode = Artisan::call('accurate:audit-sync', ['--source' => (string) $source->id]);
        $output = Artisan::output();

        $this->assertSame(1, $exitCode);
        $this->assertDatabaseHas('accurate_audit_sync_runs', [
            'accurate_audit_source_id' => $source->id,
            'status' => 'failed',
        ]);
        $this->assertStringContainsString('Firebird PDO driver is not installed', $output);
        $this->assertStringNotContainsString('10.10.10.5', $output);
        $this->assertStringNotContainsString('/secret/path/accurate.fdb', $output);
        $this->assertStringNotContainsString('SECRET_USER', $output);
        $this->assertStringNotContainsString('SECRET_PASSWORD', $output);
    }

    public function test_command_dry_run_does_not_insert_or_record_runs(): void
    {
        $source = $this->source();
        $this->fakeReader([$this->auditRow()]);

        $exitCode = Artisan::call('accurate:audit-sync', [
            '--source' => (string) $source->id,
            '--dry-run' => true,
        ]);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('writes=0', Artisan::output());
        $this->assertDatabaseCount('accurate_audit_events', 0);
        $this->assertDatabaseCount('accurate_audit_sync_runs', 0);
    }

    public function test_firebird_reader_query_uses_audit_and_users_without_login(): void
    {
        $state = new AccurateAuditSyncState([
            'last_audit_id' => '1000',
        ]);

        [$sql, $bindings] = app(PdoFirebirdAccurateAuditReader::class)->buildAuditQuery($state, 50);

        $this->assertStringContainsString('FROM AUDIT a', $sql);
        $this->assertStringContainsString('LEFT JOIN USERS u', $sql);
        $this->assertStringNotContainsString('LOGIN', strtoupper($sql));
        $this->assertSame(['1000'], $bindings);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function source(array $overrides = []): AccurateAuditSource
    {
        return AccurateAuditSource::query()->create($overrides + [
            'name' => 'Test Accurate Firebird',
            'firebird_host' => '127.0.0.1',
            'firebird_port' => 3051,
            'database_path' => 'placeholder.fdb',
            'username' => 'READONLY',
            'credential_ref' => 'ACCURATE_FIREBIRD_PASSWORD',
            'is_active' => true,
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function fakeReader(array $rows, ?\Throwable $exception = null): void
    {
        app()->instance(AccurateAuditReaderInterface::class, new class($rows, $exception) implements AccurateAuditReaderInterface
        {
            public function __construct(
                private readonly array $rows,
                private readonly ?\Throwable $exception,
            ) {}

            public function fetchAuditRows(AccurateAuditSource $source, ?AccurateAuditSyncState $state, int $limit): array
            {
                if ($this->exception) {
                    throw $this->exception;
                }

                return array_slice($this->rows, 0, $limit);
            }
        });
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function auditRow(array $overrides = []): array
    {
        return $overrides + [
            'AUDITID' => '1001',
            'USERID' => '42',
            'ACTIVITY_TIME' => '2026-05-29 10:15:00',
            'ACCURATE_USERNAME' => 'FINANCE01',
            'ACCURATE_FULLNAME' => 'Finance User',
            'SOURCE' => 'SALES',
            'TRANSTYPE' => 'UPDATE',
            'TRANSDESCRIPTION' => 'Update sales invoice',
            'INVOICENO' => 'SI-000123',
            'COMP_NAME' => 'CLIENT-01',
            'IPADDRESS' => '10.10.10.20',
            'APPVERSION' => '5.0',
            'STATUS' => 'SUCCESS',
        ];
    }
}
