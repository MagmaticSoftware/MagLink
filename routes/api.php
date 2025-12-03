<?php

use App\Http\Controllers\Api\SlugCheckController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('check-slug', SlugCheckController::class)->name('check.slug');

// Stripe Webhooks
Route::post('/stripe/webhook', [\Laravel\Cashier\Http\Controllers\WebhookController::class, 'handleWebhook'])
    ->name('stripe.webhook');
