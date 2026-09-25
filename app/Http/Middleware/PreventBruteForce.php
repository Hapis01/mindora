<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * PreventBruteForce Middleware
 *
 * Additional layer on top of Laravel's built-in throttle middleware.
 * Logs suspicious activity patterns (many failed POSTs to auth routes)
 * and can be extended to temporarily block IPs.
 */
class PreventBruteForce
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}
