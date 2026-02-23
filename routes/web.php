<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Root domain — solo responde en fedeme.test (no en subdominios de evento)
Route::domain(env('APP_DOMAIN', 'fedeme.test'))->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('home');

    // Redirect Breeze default /dashboard → admin panel
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
