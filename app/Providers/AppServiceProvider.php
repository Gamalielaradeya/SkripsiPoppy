<?php

namespace App\Providers;

use App\Services\AccurateAudit\AccurateAuditReaderInterface;
use App\Services\AccurateAudit\PdoFirebirdAccurateAuditReader;
use App\Services\Devices\DeviceOperationalStatusService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AccurateAuditReaderInterface::class, PdoFirebirdAccurateAuditReader::class);
        $this->app->singleton(DeviceOperationalStatusService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
