<?php

namespace App\Http\Middleware;

use App\Support\BusinessPageShell;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Closure;

class SetBusinessPageBusinessHubShell
{
    public function handle(Request $request, Closure $next): Response
    {
        BusinessPageShell::useBusinessHub();

        return $next($request);
    }
}
