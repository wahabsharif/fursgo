<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectUsersTo('/');
        $middleware->alias([
            'auth.groomer_spacer' => \App\Http\Middleware\EnsureGroomerSpacerAuthenticated::class,
            'business.shell.web' => \App\Http\Middleware\SetBusinessPageWebShell::class,
            'business.shell.business-hub' => \App\Http\Middleware\SetBusinessPageBusinessHubShell::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (
            \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e,
            \Illuminate\Http\Request $request
        ) {
            // Browsers sometimes replay stale Livewire AJAX POST URLs as GET requests.
            // Redirect them back to the referring page (or home) instead of showing a 405.
            if ($request->isMethod('GET') && str_contains($request->path(), '/update')) {
                $referer = $request->headers->get('referer');
                return redirect($referer ?: '/');
            }
        });
    })->create();
