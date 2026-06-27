<?php

namespace App\Console\Commands;

use App\Services\Alerts\AlertDetectionService;
use Illuminate\Console\Command;

class DetectAlertsCommand extends Command
{
    protected $signature = 'alerts:detect';

    protected $description = 'Detect contextual monitoring alerts and send Telegram notifications when configured.';

    public function handle(AlertDetectionService $detectionService): int
    {
        $summary = $detectionService->detect();

        $this->info(sprintf(
            'Alert detection complete. created=%d updated=%d resolved=%d notifications=%d',
            $summary['created'],
            $summary['updated'],
            $summary['resolved'],
            $summary['notifications'],
        ));

        return self::SUCCESS;
    }
}
