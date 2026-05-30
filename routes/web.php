<?php

use App\Http\Controllers\AccurateAuditController;
use App\Http\Controllers\AdvancedLogController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\RemoteActionController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard.index');

    Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
    Route::get('/devices/{id}', [DeviceController::class, 'show'])->name('devices.show');
    Route::post('/devices/{device}/remote-actions/rdp', [RemoteActionController::class, 'storeRdp'])->name('devices.remote-actions.rdp');
    Route::post('/devices/{device}/remote-actions/restart', [RemoteActionController::class, 'storeRestart'])->name('devices.remote-actions.restart');

    Route::get('/accurate-audit', [AccurateAuditController::class, 'index'])->name('accurate-audit.index');
    Route::get('/accurate-audit/{id}', [AccurateAuditController::class, 'show'])->name('accurate-audit.show');

    Route::get('/incidents', [IncidentController::class, 'index'])->name('incidents.index');
    Route::get('/incidents/{id}', [IncidentController::class, 'show'])->name('incidents.show');

    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');
    Route::get('/alerts/{id}', [AlertController::class, 'show'])->name('alerts.show');
    Route::post('/alerts/{alert}/acknowledge', [AlertController::class, 'acknowledge'])->name('alerts.acknowledge');
    Route::post('/alerts/{alert}/resolve', [AlertController::class, 'resolve'])->name('alerts.resolve');

    Route::get('/remote-actions', [RemoteActionController::class, 'index'])->name('remote-actions.index');
    Route::get('/remote-actions/{remoteAction}/rdp-file', [RemoteActionController::class, 'downloadRdpFile'])->name('remote-actions.rdp-file');
    Route::get('/remote-actions/{id}', [RemoteActionController::class, 'show'])->name('remote-actions.show');

    Route::get('/advanced-logs', [AdvancedLogController::class, 'index'])->name('advanced-logs.index');
    Route::get('/advanced-logs/{id}', [AdvancedLogController::class, 'show'])->name('advanced-logs.show');

    Route::get('/settings', SettingController::class)->name('settings.index');
});
