<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnsureAgentApiEnabled
{
    public function handle(Request $request, Closure $next): JsonResponse
    {
        if (! config('monitoring.remote_action.agent_api_enabled', false)) {
            return response()->json([
                'message' => 'Agent API is disabled. Enable it via Settings.',
            ], 503);
        }

        return $next($request);
    }
}