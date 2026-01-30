<?php

use App\Http\Controllers\LinkController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\QrCodeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


// Rotte admin spostate in admin.php
if (file_exists(__DIR__.'/admin.php')) {
    require __DIR__.'/admin.php';
}

Route::domain(config('app.url'))->middleware('web')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Welcome');
    })->name('home');

    Route::get('{page:slug}', [PageController::class, 'showPublic'])->name('pages.show.public');
});

Route::domain(config('app.short_url'))->middleware('web')->group(function () {
    //REDIRECT ALLA HOME
    Route::get('/', function () {
        return redirect(config('app.url'));
    })->name('home.short');

    // Public redirect routes
    // QR Codes: always use /q/ prefix (6 chars auto-generated or custom)
    Route::get('q/{slug}', [\App\Http\Controllers\RedirectController::class, 'qrCode'])
        ->name('redirect.qrcode');

    // Links: catch-all route (6 chars auto-generated or custom slugs)
    // Supports: auto-generated (6 chars) and custom slugs (3-100 chars, kebab-case)
    Route::get('{slug}', [\App\Http\Controllers\RedirectController::class, 'link'])
        ->where('slug', '[a-z0-9]+(-[a-z0-9]+)*')
        ->name('redirect.slug');
});

// Auth routes - on tenant domain app.maglink.localhost
Route::domain(config('app.tenant_url'))->middleware('web')->group(function () {
    require __DIR__.'/auth.php';
});