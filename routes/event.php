<?php

declare(strict_types=1);

use App\Interfaces\Http\Controllers\Auth\EventAccessController;
use App\Interfaces\Http\Controllers\Public\EventLandingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tenant / Event subdomain routes
| All routes here go through TenantResolver + EventAccessMiddleware.
|--------------------------------------------------------------------------
*/

Route::middleware([
    'web',
    \App\Interfaces\Http\Middleware\TenantResolver::class,
])->group(function () {

    // Access code gate (no EventAccessMiddleware here to avoid redirect loop)
    Route::get('/access', [EventAccessController::class, 'show'])
        ->name('event.access.form');

    Route::post('/access', [EventAccessController::class, 'store'])
        ->name('event.access.store')
        ->middleware('throttle:5,1');

    // Protected public routes
    Route::middleware(\App\Interfaces\Http\Middleware\EventAccessMiddleware::class)
        ->group(function () {
            Route::get('/', [EventLandingController::class, 'show'])
                ->name('event.landing');
        });
});
