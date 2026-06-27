<?php

namespace App\Console\Commands;

use App\Services\Incidents\IncidentCorrelationService;
use Illuminate\Console\Command;

class CorrelateIncidentsCommand extends Command
{
    protected $signature = 'incidents:correlate';

    protected $description = 'Korelasikan alert yang terbuka menjadi incident operasional.';

    public function handle(IncidentCorrelationService $correlationService): int
    {
        $summary = $correlationService->correlate();

        $this->info(sprintf(
            'Incident correlation complete. created=%d updated=%d',
            $summary['created'],
            $summary['updated'],
        ));

        return self::SUCCESS;
    }
}