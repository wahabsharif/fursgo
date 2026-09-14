<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Closure;

class EnsureWebOrGroomerSpacerAuthenticated
{
    /**
     * Allow either the web or groomer_spacer session guard.
     */
    public function handle(Request $request, Closure $next): Response
    {
        foreach (['web', 'groomer_spacer'] as $guard) {
            if (Auth::guard($guard)->check()) {
                Auth::shouldUse($guard);
                $request->setUserResolver(fn(?string $guardName = null) => Auth::guard($guardName)->user());

                return $next($request);
            }
        }

        if ($request->expectsJson()) {
            abort(401);
        }

        return redirect()->guest('/login');
    }
}
