<?php

use Illuminate\Support\Facades\Hash;

test('password can be updated', function () {
    $user = $this->createTenantUser();
    $url = route('password.edit', ['tenant' => $user->tenant_id]);

    $this->actingAs($user)
        ->from($url)
        ->put(route('password.update', ['tenant' => $user->tenant_id]), [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect($url);

    expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
});

test('correct password must be provided to update password', function () {
    $user = $this->createTenantUser();
    $url = route('password.edit', ['tenant' => $user->tenant_id]);

    $this->actingAs($user)
        ->from($url)
        ->put(route('password.update', ['tenant' => $user->tenant_id]), [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertSessionHasErrors('current_password')
        ->assertRedirect($url);
});
