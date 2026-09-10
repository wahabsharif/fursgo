<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Closure;

class EnsureGroomerSpacerAuthenticated
{
    /**
     * Ensure the request is authenticated with the groomer_spacer guard.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('groomer_spacer')->check()) {
            return redirect('/login-groomer-space');
        }

        Auth::shouldUse('groomer_spacer');
        $request->setUserResolver(fn(?string $guard = null) => Auth::guard($guard)->user());

        return $next($request);
    }
}
