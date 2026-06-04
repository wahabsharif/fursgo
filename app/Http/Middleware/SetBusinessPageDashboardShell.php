<?php

namespace App\Http\Middleware;

use App\Support\BusinessPageShell;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetBusinessPageDashboardShell
{
    public function handle(Request $request, Closure $next): Response
    {
        BusinessPageShell::useDashboard();

        return $next($request);
    }
}
