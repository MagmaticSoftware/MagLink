<?php

function dashboardFor($test, $user)
{
    return $test->actingAs($user)->get(route('tenant.index', ['tenant' => $user->tenant_id]));
}

test('new users who can still start a trial can access the app', function () {
    $user = $this->createTenantUser(['trial_ends_at' => null]);

    dashboardFor($this, $user)->assertOk();
});

test('users on an active trial can access the app', function () {
    $user = $this->createTenantUser(['trial_ends_at' => now()->addDays(10)]);

    expect($user->onTrial())->toBeTrue()
        ->and($user->trialDaysLeft())->toBeGreaterThanOrEqual(9);

    dashboardFor($this, $user)->assertOk();
});

test('free plan users can access the app', function () {
    $user = $this->createTenantUser([
        'trial_ends_at' => now()->subDay(),
        'free_plan_started_at' => now(),
    ]);

    dashboardFor($this, $user)->assertOk();
});

test('pro lifetime users can access the app even with an expired trial', function () {
    $user = $this->createTenantUser([
        'trial_ends_at' => now()->subDay(),
        'pro_lifetime_started_at' => now(),
    ]);

    expect($user->hasAccess())->toBeTrue();

    dashboardFor($this, $user)->assertOk();
});

test('subscribed users can access the app', function () {
    $user = $this->createTenantUser(['trial_ends_at' => now()->subDay()]);
    $this->subscribeTo($user, 'professional');

    dashboardFor($this, $user)->assertOk();
});

test('users with an expired trial and no plan are sent to the plans page', function () {
    $user = $this->createTenantUser(['trial_ends_at' => now()->subDay()]);

    expect($user->hasAccess())->toBeFalse();

    dashboardFor($this, $user)
        ->assertRedirect(route('plans.index', ['tenant' => $user->tenant_id]));
});

test('users can not access another tenant', function () {
    $user = $this->createTenantUser();
    $other = $this->createTenantUser();

    $this->actingAs($user)
        ->get(route('tenant.index', ['tenant' => $other->tenant_id]))
        ->assertStatus(403);
});

test('starting a trial sets a 30 day trial only once', function () {
    $user = $this->createTenantUser(['trial_ends_at' => null]);

    $user->startTrial();
    $endsAt = $user->trial_ends_at;

    expect($user->onTrial())->toBeTrue()
        ->and((int) round(now()->diffInDays($endsAt)))->toBe(30);

    $user->startTrial();

    expect($user->trial_ends_at->equalTo($endsAt))->toBeTrue();
});

test('pro lifetime users are redirected away from checkout', function () {
    $user = $this->createTenantUser(['pro_lifetime_started_at' => now()]);

    $this->actingAs($user)
        ->get(route('checkout', ['tenant' => $user->tenant_id, 'plan' => 'professional', 'billing' => 'monthly']))
        ->assertRedirect(route('tenant.index', ['tenant' => $user->tenant_id]))
        ->assertSessionHas('info');
});
