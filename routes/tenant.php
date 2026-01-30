<?php

declare(strict_types=1);

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\PageBlockController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\QrCodeController;
use App\Http\Middleware\RedirectIfNotSubscribed;
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
    
    // ==========================================
    // Routes senza verifica abbonamento (piani, checkout, billing)
    // ==========================================
    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
    Route::get('/plans/{plan}', [PlanController::class, 'show'])->name('plans.show');
    Route::post('/plans/start-trial', [PlanController::class, 'startTrial'])->name('plans.start-trial');
    
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/free', [CheckoutController::class, 'handleFreePlan'])->name('checkout.free');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
    Route::get('/billing-portal', [CheckoutController::class, 'billingPortal'])->name('billing.portal');
    
    // ==========================================
    // Routes che richiedono abbonamento/trial attivo
    // ==========================================
    Route::middleware([RedirectIfNotSubscribed::class])->group(function () {
        Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])
            ->name('tenant.index');

        // Analytics routes
        Route::get('links/{link}/analytics', [App\Http\Controllers\AnalyticsController::class, 'link'])
            ->name('links.analytics');
        Route::get('qrcodes/{qrcode}/analytics', [App\Http\Controllers\AnalyticsController::class, 'qrcode'])
            ->name('qrcodes.analytics');

        Route::resource('links', LinkController::class)->names('links');
        Route::resource('qrcodes', QrCodeController::class)->names('qrcodes');
        Route::post('pages/{page}/upload-profile-image', [PageController::class, 'uploadProfileImage'])->name('pages.upload-profile-image');
        Route::delete('pages/{page}/delete-profile-image', [PageController::class, 'deleteProfileImage'])->name('pages.delete-profile-image');
        Route::resource('pages', PageController::class)->names('pages');
        
        // Block routes - order matters! Put specific routes before resource
        Route::delete('page-blocks/delete-all/{page}', [PageBlockController::class, 'deleteAllForPage'])->name('blocks.delete-all');
        Route::post('page-blocks/positions', [PageBlockController::class, 'updatePositions'])->name('blocks.positions');
        Route::post('page-blocks/{block}/position', [PageBlockController::class, 'updatePosition'])->name('blocks.position');
        Route::post('page-blocks/{block}/size', [PageBlockController::class, 'updateSize'])->name('blocks.size');
        Route::post('page-blocks/fetch-url-metadata', [PageBlockController::class, 'fetchUrlMetadata'])->name('blocks.fetch-url-metadata');
        Route::resource('page-blocks', PageBlockController::class)
            ->names('page-blocks');
    });
});

Route::domain(config('app.tenant_url'))->middleware('web')->group(function () {
    // Fallback per app.maglink.it senza tenant - redirect a homepage pubblica
    Route::get('/', function () {
        return redirect()->route('home');
    });
});

// Settings routes - require tenancy
require __DIR__.'/settings.php';



