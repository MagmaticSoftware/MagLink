<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::domain('admin.maglink.localhost')->group(function () {
    Route::get('admin', function () {
        return Inertia::render('admin/Index');
    })->name('admin.index');
});

require __DIR__.'/auth.php';
require __DIR__.'/settings.php';
