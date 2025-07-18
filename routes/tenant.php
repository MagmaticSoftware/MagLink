<?php

declare(strict_types=1);

use App\Http\Controllers\LinkController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\QrCodeController;
use App\Http\Middleware\SetDefaultTenantForUrls;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    InitializeTenancyByPath::class,
    PreventAccessFromCentralDomains::class,
    'web',
    'auth',
    SetDefaultTenantForUrls::class
])->prefix('/{tenant}')->group(function () {
    
    Route::get('/dashboard', function () {
        Log::info('Accessing tenant dashboard', [
            'tenant' => tenant(),
            'tenant_id' => tenant('id'),
            'request_path' => request()->path(),
            'url' => request()->url(),
        ]);
        return Inertia::render('tenant/Dashboard');
    })->name('tenant.index');

    Route::resource('links', LinkController::class)->names('links');
    Route::resource('qrcodes', QrCodeController::class)->names('qrcodes');
    Route::resource('pages', PageController::class)->names('pages');
    Route::resource('page-blocks', \App\Http\Controllers\PageBlockController::class)
        ->names('page-blocks');
    Route::post('page-blocks/{block}/position', [\App\Http\Controllers\PageBlockController::class, 'updatePosition']);
    Route::post('page-blocks/{block}/size', [\App\Http\Controllers\PageBlockController::class, 'updateSize']);
});
