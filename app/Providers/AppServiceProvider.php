<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Laravel\Cashier\Events\WebhookReceived;
use App\Listeners\UpdateSubscriptionPeriodEnd;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registra il listener per i webhook di Stripe
        Event::listen(
            WebhookReceived::class,
            UpdateSubscriptionPeriodEnd::class,
        );
    }
}
