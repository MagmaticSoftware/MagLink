<?php

test('profile page is displayed', function () {
    $user = $this->createTenantUser();

    $this->actingAs($user)
        ->get(route('profile.edit', ['tenant' => $user->tenant_id]))
        ->assertOk();
});

test('profile information can be updated', function () {
    $user = $this->createTenantUser();

    $this->actingAs($user)
        ->patch(route('profile.update', ['tenant' => $user->tenant_id]), [
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'email' => 'test@example.com',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit', ['tenant' => $user->tenant_id]));

    $user->refresh();

    expect($user->first_name)->toBe('Mario')
        ->and($user->last_name)->toBe('Rossi')
        ->and($user->email)->toBe('test@example.com')
        ->and($user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = $this->createTenantUser();

    $this->actingAs($user)
        ->patch(route('profile.update', ['tenant' => $user->tenant_id]), [
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'email' => $user->email,
        ])
        ->assertSessionHasNoErrors();

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('user can delete their account', function () {
    $user = $this->createTenantUser();

    $this->actingAs($user)
        ->delete(route('profile.destroy', ['tenant' => $user->tenant_id]), [
            'password' => 'password',
        ])
        ->assertSessionHasNoErrors();

    $this->assertGuest();
    expect($user->fresh()->trashed())->toBeTrue();
});

test('correct password must be provided to delete account', function () {
    $user = $this->createTenantUser();
    $url = route('profile.edit', ['tenant' => $user->tenant_id]);

    $this->actingAs($user)
        ->from($url)
        ->delete(route('profile.destroy', ['tenant' => $user->tenant_id]), [
            'password' => 'wrong-password',
        ])
        ->assertSessionHasErrors('password')
        ->assertRedirect($url);

    expect($user->fresh()->trashed())->toBeFalse();
});
