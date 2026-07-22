<?php

namespace App\Http\Middleware;

use App\Support\AccountLoginSessions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Closure;

class TrackAccountLoginSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $guard = Auth::guard('groomer_spacer')->check()
            ? 'groomer_spacer'
            : (Auth::guard('web')->check() ? 'web' : null);

        if ($guard === null) {
            return $response;
        }

        $owner = Auth::guard($guard)->user();
        if ($owner === null) {
            return $response;
        }

        AccountLoginSessions::touch($owner, $request, $guard);

        return $response;
    }
}
