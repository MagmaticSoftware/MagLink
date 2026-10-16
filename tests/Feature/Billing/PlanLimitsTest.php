<?php

use Inertia\Testing\AssertableInertia as Assert;

test('users without a plan get the free limits', function () {
    $user = $this->createTenantUser();

    expect($user->currentPlanKey())->toBeNull()
        ->and($user->getPlanLimits())->toBe(config('subscriptions.plans.free.limits'));
});

test('free plan users get the free limits', function () {
    $user = $this->createTenantUser(['free_plan_started_at' => now()]);

    expect($user->onFreePlan())->toBeTrue()
        ->and($user->currentPlanKey())->toBe('free')
        ->and($user->currentPlanName())->toBe('Free')
        ->and($user->getPlanLimits()['links'])->toBe(5);
});

test('pro lifetime users get unlimited limits', function () {
    $user = $this->createTenantUser(['pro_lifetime_started_at' => now()]);

    expect($user->onProLifetime())->toBeTrue()
        ->and($user->currentPlanKey())->toBe('enterprise')
        ->and($user->currentPlanName())->toBe('Pro Lifetime')
        ->and($user->getPlanLimits())->toBe(config('subscriptions.plans.enterprise.limits'))
        ->and($user->getPlanLimits()['links'])->toBe(-1);
});

test('pro lifetime takes precedence over a previous free plan', function () {
    $user = $this->createTenantUser([
        'free_plan_started_at' => now()->subMonth(),
        'pro_lifetime_started_at' => now(),
    ]);

    expect($user->onFreePlan())->toBeFalse()
        ->and($user->currentPlanKey())->toBe('enterprise')
        ->and($user->getPlanLimits()['links'])->toBe(-1);
});

test('pro lifetime takes precedence over a stripe subscription', function () {
    $user = $this->createTenantUser(['pro_lifetime_started_at' => now()]);
    $this->subscribeTo($user, 'professional');

    expect($user->currentPlanKey())->toBe('enterprise')
        ->and($user->currentPlanName())->toBe('Pro Lifetime');
});

test('paid subscriptions map to the plan limits', function (string $plan, string $interval) {
    $user = $this->createTenantUser();
    $this->subscribeTo($user, $plan, $interval);

    expect($user->currentPlanKey())->toBe($plan)
        ->and($user->currentPlanName())->toBe(config("subscriptions.plans.{$plan}.name"))
        ->and($user->getPlanLimits())->toBe(config("subscriptions.plans.{$plan}.limits"));
})->with([
    ['professional', 'monthly'],
    ['professional', 'yearly'],
    ['enterprise', 'monthly'],
    ['enterprise', 'yearly'],
]);

test('expired subscriptions fall back to the free limits', function () {
    $user = $this->createTenantUser();
    $this->subscribeTo($user, 'professional', status: 'canceled', attributes: ['ends_at' => now()->subDay()]);

    expect($user->currentPlanKey())->toBeNull()
        ->and($user->getPlanLimits())->toBe(config('subscriptions.plans.free.limits'))
        ->and($user->lapsedSubscription())->not->toBeNull();
});

test('dashboard shares the backend limits for pro lifetime users', function () {
    $user = $this->createTenantUser([
        'free_plan_started_at' => now()->subMonth(),
        'pro_lifetime_started_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('tenant.index', ['tenant' => $user->tenant_id]))
        ->assertInertia(fn (Assert $page) => $page
            ->where('billing.onProLifetime', true)
            ->where('billing.onFreePlan', false)
            ->where('billing.currentPlanName', 'Pro Lifetime')
            ->where('billing.limits.links', -1)
            ->where('billing.limits.qrcodes', -1)
            ->where('billing.limits.pages', -1)
            ->etc()
        );
});

test('dashboard shares the free limits for free plan users', function () {
    $user = $this->createTenantUser(['free_plan_started_at' => now()]);

    $this->actingAs($user)
        ->get(route('tenant.index', ['tenant' => $user->tenant_id]))
        ->assertInertia(fn (Assert $page) => $page
            ->where('billing.onFreePlan', true)
            ->where('billing.limits.links', 5)
            ->etc()
        );
});
