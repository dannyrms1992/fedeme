<?php

use App\Domain\Tenant\Exceptions\TenantNotFoundException;
use App\Interfaces\Http\Middleware\EventAccessMiddleware;
use App\Interfaces\Http\Middleware\TenantResolver;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Event subdomain routes — ONLY match *.fedeme.ec subdomains
            // Without this domain constraint every request to fedeme.test/ also
            // matches GET / and crashes because TenantContext is not resolved.
            $appDomain = ltrim(env('APP_DOMAIN', 'fedeme.ec'), '.');
            \Illuminate\Support\Facades\Route::domain('{subdomain}.' . $appDomain)
                ->middleware('web')
                ->group(base_path('routes/event.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Named middleware aliases
        $middleware->alias([
            'tenant'       => TenantResolver::class,
            'event.access' => EventAccessMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Tenant not found → clean 404 without leaking internals
        $exceptions->render(function (TenantNotFoundException $e, Request $request) {
            abort(404);
        });
    })->create();

