<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        /*
        if (! $request->user()->hasRole($role)) {
            // Redirect...
        }
        */
        return $next($request);
    }
}
