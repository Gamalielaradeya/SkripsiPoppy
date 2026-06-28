<?php

namespace App\Services\AccurateAudit;

use Illuminate\Support\Carbon;

class AccurateAuditMapper
{
    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    public function mapRow(array $row): array
    {
        $normalized = $this->normalizeKeys($row);

        $auditId = $this->stringOrNull($normalized['AUDITID'] ?? null);
        $activityTime = $this->dateOrNull($normalized['ACTIVITY_TIME'] ?? null);

        if ($auditId === null) {
            throw new AccurateAuditReaderException('Firebird audit row is missing AUDITID.');
        }

        if ($activityTime === null) {
            throw new AccurateAuditReaderException('Firebird audit row is missing ACTIVITY_TIME.');
        }

        $mapped = [
            'accurate_audit_id' => $auditId,
            'accurate_user_id' => $this->stringOrNull($normalized['USERID'] ?? null),
            'activity_time' => $activityTime,
            'accurate_username' => $this->stringOrNull($normalized['ACCURATE_USERNAME'] ?? null),
            'accurate_fullname' => $this->stringOrNull($normalized['ACCURATE_FULLNAME'] ?? null),
            'source' => $this->stringOrNull($normalized['SOURCE'] ?? null),
            'transaction_type' => $this->stringOrNull($normalized['TRANSTYPE'] ?? null),
            'transaction_description' => $this->stringOrNull($normalized['TRANSDESCRIPTION'] ?? null),
            'invoice_no' => $this->stringOrNull($normalized['INVOICENO'] ?? null),
            'comp_name' => $this->stringOrNull($normalized['COMP_NAME'] ?? null),
            'ip_address' => $this->stringOrNull($normalized['IPADDRESS'] ?? null),
            'app_version' => $this->stringOrNull($normalized['APPVERSION'] ?? null),
            'status' => $this->stringOrNull($normalized['STATUS'] ?? null),
            'raw_payload' => $row,
            'synced_at' => now(),
        ];

        $mapped['hash'] = $this->hash($mapped);

        return $mapped;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private function normalizeKeys(array $row): array
    {
        $normalized = [];

        foreach ($row as $key => $value) {
            if (is_string($key)) {
                $normalized[strtoupper($key)] = $value;
            }
        }

        return $normalized;
    }

    private function stringOrNull(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function dateOrNull(mixed $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Carbon::parse($value, 'UTC')->setTimezone(config('app.timezone'));
        } catch (\Throwable) {
            throw new AccurateAuditReaderException('Firebird audit row has invalid ACTIVITY_TIME.');
        }
    }

    /**
     * @param  array<string, mixed>  $mapped
     */
    private function hash(array $mapped): string
    {
        return hash('sha256', implode('|', [
            $mapped['accurate_audit_id'] ?? '',
            $mapped['activity_time']?->toIso8601String() ?? '',
            $mapped['accurate_username'] ?? '',
            $mapped['source'] ?? '',
            $mapped['transaction_type'] ?? '',
            $mapped['transaction_description'] ?? '',
            $mapped['invoice_no'] ?? '',
        ]));
    }
}
