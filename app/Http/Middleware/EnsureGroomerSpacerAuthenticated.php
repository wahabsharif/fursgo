<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureGroomerSpacerAuthenticated
{
    /**
     * Ensure the request is authenticated with the groomer_spacer guard.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('groomer_spacer')->check()) {
            return redirect()->route('login-groomer-space');
        }

        return $next($request);
    }
}
