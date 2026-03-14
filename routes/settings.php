<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Middleware\SetDefaultTenantForUrls;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::domain(config('app.tenant_url'))->middleware([
    InitializeTenancyByPath::class,
    PreventAccessFromCentralDomains::class,
    'web',
    'auth',
    'verified',
    SetDefaultTenantForUrls::class,
])->prefix('/{tenant}')->group(function () {
    Route::get('settings', function () {
        return redirect()->route('profile.edit', ['tenant' => request()->route('tenant')]);
    });

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('settings/password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance');
});
