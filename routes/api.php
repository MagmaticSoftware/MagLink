<?php

use App\Http\Controllers\Api\SlugCheckController;
use App\Http\Controllers\QrCodeImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('check-slug', SlugCheckController::class)->name('check.slug');

// QR Code Image Generation
Route::get('qrcode/preview', [QrCodeImageController::class, 'preview'])->name('qrcode.preview');
Route::get('qrcode/{qrcode:slug}/download/{format?}', [QrCodeImageController::class, 'download'])->name('qrcode.download');

// Stripe Webhooks
Route::post('/stripe/webhook', [\Laravel\Cashier\Http\Controllers\WebhookController::class, 'handleWebhook'])
    ->name('stripe.webhook');
