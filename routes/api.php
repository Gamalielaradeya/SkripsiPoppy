<?php

use App\Http\Controllers\AgentApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('agent')->middleware('agent.api.enabled')->group(function (): void {
    Route::post('/register', [AgentApiController::class, 'register'])->name('api.agent.register');
    Route::post('/heartbeat', [AgentApiController::class, 'heartbeat'])->name('api.agent.heartbeat');
    Route::get('/commands/pending', [AgentApiController::class, 'pendingCommands'])->name('api.agent.commands.pending');
    Route::post('/commands/{remoteAction}/result', [AgentApiController::class, 'commandResult'])->name('api.agent.commands.result');
});