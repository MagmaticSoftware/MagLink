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
        dd('short domain');
    })->name('home.short');

    // Slug-based routing: 6 chars = Link, 8 chars = QR Code
    Route::get('{slug}', function ($slug) {
        // QR codes have 8 characters, Links have 6 characters
        if (strlen($slug) === 8) {
            $qrcode = \App\Models\QrCode::where('slug', $slug)->firstOrFail();
            return app(\App\Http\Controllers\QrCodeController::class)->showPublicQr($qrcode);
        } else {
            $link = \App\Models\Link::where('slug', $slug)->firstOrFail();
            return app(\App\Http\Controllers\LinkController::class)->showPublicShort($link);
        }
    })->where('slug', '[a-z0-9]{6,8}');
});