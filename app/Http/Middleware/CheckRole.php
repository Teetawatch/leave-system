<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
             abort(403, 'Unauthorized action.');
        }

        // Allow if user has ANY of the required roles
        // We can check against the user's role column directly or use a helper
        if (in_array($request->user()->role, $roles)) {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}
