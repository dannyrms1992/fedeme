<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Panel routes
| Protected by auth + verified. Role check via Spatie policies.
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', fn () => view('admin.dashboard'))->name('dashboard');

    // Events CRUD
    Route::resource('events', \App\Interfaces\Http\Controllers\Admin\EventController::class);

    // Access code management (nested under event)
    Route::prefix('events/{event}/access')->name('events.access.')->group(function () {
        Route::get('/', [\App\Interfaces\Http\Controllers\Admin\EventAccessConfigController::class, 'edit'])
            ->name('edit');
        Route::patch('/', [\App\Interfaces\Http\Controllers\Admin\EventAccessConfigController::class, 'update'])
            ->name('update');
    });

    // Image uploads
    Route::post('events/{event}/logo', [\App\Interfaces\Http\Controllers\Admin\EventImageController::class, 'uploadLogo'])
        ->name('events.logo.upload');
    Route::post('events/{event}/carousel', [\App\Interfaces\Http\Controllers\Admin\EventImageController::class, 'uploadCarousel'])
        ->name('events.carousel.upload');
    Route::delete('events/{event}/carousel/{index}', [\App\Interfaces\Http\Controllers\Admin\EventImageController::class, 'deleteCarouselImage'])
        ->name('events.carousel.delete');

    // Module content editor
    Route::get('events/{event}/modules', [\App\Interfaces\Http\Controllers\Admin\EventModuleController::class, 'edit'])
        ->name('events.modules.edit');
    Route::patch('events/{event}/modules/{module}', [\App\Interfaces\Http\Controllers\Admin\EventModuleController::class, 'update'])
        ->name('events.modules.update');
    Route::post('events/{event}/modules/{module}/reorder', [\App\Interfaces\Http\Controllers\Admin\EventModuleController::class, 'reorder'])
        ->name('events.modules.reorder');
});
