<?php

namespace App\Http\Middleware;

use App\Support\BusinessPageShell;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetBusinessPageWebShell
{
    public function handle(Request $request, Closure $next): Response
    {
        BusinessPageShell::useWeb();

        if ($request->routeIs('home')) {
            BusinessPageShell::clearPublicBusinessChrome();
        }

        if ($request->routeIs('business-landing-page')) {
            BusinessPageShell::usePublicBusinessChrome();
        }

        return $next($request);
    }
}
