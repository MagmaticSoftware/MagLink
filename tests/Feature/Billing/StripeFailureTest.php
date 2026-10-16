<?php

use Illuminate\Support\Facades\Log;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Cashier\Cashier;

beforeEach(function () {
    // Stripe irraggiungibile: qualsiasi chiamata API fallisce
    $this->originalApiBase = Cashier::$apiBaseUrl;
    Cashier::$apiBaseUrl = 'http://127.0.0.1:9';
    config(['cashier.secret' => 'sk_test_fake', 'cache.default' => 'database']);
});

afterEach(function () {
    Cashier::$apiBaseUrl = $this->originalApiBase;
});

test('pages still load when stripe can not return the subscription', function () {
    Log::spy();

    $user = $this->createTenantUser();
    $this->subscribeTo($user, 'professional', attributes: ['current_period_end' => null]);

    $this->actingAs($user)
        ->get(route('tenant.index', ['tenant' => $user->tenant_id]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('subscription.key', 'professional')
            ->where('subscription.current_period_end', null)
            ->etc()
        );

    $this->actingAs($user)
        ->get(route('plans.index', ['tenant' => $user->tenant_id]))
        ->assertOk();

    // Dopo il primo errore non si ritenta la chiamata per qualche minuto
    Log::shouldHaveReceived('warning')->once();
});

test('the stored period end is used without calling stripe', function () {
    $user = $this->createTenantUser();
    $this->subscribeTo($user, 'professional', attributes: ['current_period_end' => now()->addDays(12)]);

    $this->actingAs($user)
        ->get(route('tenant.index', ['tenant' => $user->tenant_id]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('subscription.current_period_end', fn ($value) => $value !== null)
            ->etc()
        );
});
