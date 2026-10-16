<?php

use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

test('email verification screen can be rendered', function () {
    $user = $this->createTenantUser(['email_verified_at' => null]);

    $this->actingAs($user)->get(route('verification.notice'))->assertOk();
});

test('verified users are redirected from the verification screen to the dashboard', function () {
    $user = $this->createTenantUser();

    $this->actingAs($user)
        ->get(route('verification.notice'))
        ->assertRedirect(route('tenant.index', ['tenant' => $user->tenant_id]));
});

test('email can be verified', function () {
    $user = $this->createTenantUser(['email_verified_at' => null]);

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    Event::assertDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    $response->assertRedirect(route('tenant.index', ['tenant' => $user->tenant_id]).'?verified=1');
});

test('email is not verified with invalid hash', function () {
    $user = $this->createTenantUser(['email_verified_at' => null]);

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('wrong-email')]
    );

    $this->actingAs($user)->get($verificationUrl);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('unverified users can not access the dashboard', function () {
    $user = $this->createTenantUser(['email_verified_at' => null]);

    $this->actingAs($user)
        ->get(route('tenant.index', ['tenant' => $user->tenant_id]))
        ->assertRedirect(route('verification.notice'));
});
