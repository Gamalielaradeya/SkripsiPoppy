<?php

namespace App\Services\AccurateAudit;

use App\Models\AccurateAuditSource;
use App\Models\AccurateAuditSyncState;

interface AccurateAuditReaderInterface
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function fetchAuditRows(AccurateAuditSource $source, ?AccurateAuditSyncState $state, int $limit): array;
}
