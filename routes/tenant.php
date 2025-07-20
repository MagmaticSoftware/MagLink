<?php

declare(strict_types=1);

use App\Http\Controllers\LinkController;
use App\Http\Controllers\PageBlockController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\QrCodeController;
use App\Http\Middleware\SetDefaultTenantForUrls;
use Illuminate\Support\Facades\Auth;
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

Route::domain(config('app.tenant_url'))->middleware([
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
    Route::resource('page-blocks', PageBlockController::class)
        ->names('page-blocks');
    Route::post('page-blocks/positions', [PageBlockController::class, 'updatePositions'])->name('blocks.positions');
    Route::post('page-blocks/{block}/position', [PageBlockController::class, 'updatePosition'])->name('blocks.position');
    Route::post('page-blocks/{block}/size', [PageBlockController::class, 'updateSize'])->name('blocks.size');
});

Route::domain(config('app.tenant_url'))->middleware('web')->group(function () {
    Route::get('/', function () {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        // Se loggato, redirigi alla dashboard del tenant
        return redirect()->route('tenant.index', ['tenant' => Auth::user()->tenant_id]);
    })->name('home');
    require __DIR__.'/auth.php';
    require __DIR__.'/settings.php';
});



