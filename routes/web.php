<?php

use App\Http\Controllers\PageController;
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