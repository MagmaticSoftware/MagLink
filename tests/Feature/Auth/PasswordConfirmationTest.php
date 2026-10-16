<?php

test('confirm password screen can be rendered', function () {
    $user = $this->createTenantUser();

    $this->actingAs($user)->get(route('password.confirm'))->assertOk();
});

test('password can be confirmed', function () {
    $user = $this->createTenantUser();

    $response = $this->actingAs($user)->post(route('password.confirm'), [
        'password' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('tenant.index', ['tenant' => $user->tenant_id]));
});

test('password is not confirmed with invalid password', function () {
    $user = $this->createTenantUser();

    $response = $this->actingAs($user)->post(route('password.confirm'), [
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors('password');
});
