<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('admin', function () {
        return 'This is your admin application.';
    })->name('admin.index');

    require __DIR__.'/auth.php';
    require __DIR__.'/settings.php';
