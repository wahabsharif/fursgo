<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAuthenticated
{
    /**
     * Ensure the request is authenticated as an active admin user.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin() || !$user->isActive()) {
            if ($request->expectsJson()) {
                abort(401);
            }

            return redirect()->guest(route('admin.login'));
        }

        return $next($request);
    }
}
