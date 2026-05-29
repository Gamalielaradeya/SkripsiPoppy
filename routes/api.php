<?php

use App\Http\Controllers\AgentApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('agent')->group(function (): void {
    Route::post('/register', [AgentApiController::class, 'register'])->name('api.agent.register');
    Route::post('/heartbeat', [AgentApiController::class, 'heartbeat'])->name('api.agent.heartbeat');
    Route::get('/commands/pending', [AgentApiController::class, 'pendingCommands'])->name('api.agent.commands.pending');
});
