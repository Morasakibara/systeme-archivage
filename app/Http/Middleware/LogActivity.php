<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class LogActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Logger uniquement les actions importantes
        if ($this->shouldLog($request)) {
            $this->logActivity($request, $response);
        }

        return $response;
    }

    private function shouldLog(Request $request): bool
    {
        $methods = ['POST', 'PUT', 'PATCH', 'DELETE'];
        $routes = ['dossiers', 'entreprises', 'users', 'documents'];

        if (!in_array($request->method(), $methods)) {
            return false;
        }

        foreach ($routes as $route) {
            if (str_contains($request->path(), $route)) {
                return true;
            }
        }

        return false;
    }

    private function logActivity(Request $request, Response $response): void
    {
        $user = auth()->user();

        Log::info('User Activity', [
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? $user->name : 'Guest',
            'action' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status_code' => $response->getStatusCode(),
            'timestamp' => now(),
        ]);
    }
}
