<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::domain(config('app.admin_url'))->middleware(['web', 'auth'])->group(function () {
    Route::get('admin', function () {
        return Inertia::render('admin/Index');
    })->name('admin.index');
});
