<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $this->get(route('login'))->assertOk();
});

test('users can authenticate using the login screen', function () {
    $user = $this->createTenantUser();

    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('tenant.index', ['tenant' => $user->tenant_id]));
});

test('users can not authenticate with invalid password', function () {
    $user = $this->createTenantUser();

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = $this->createTenantUser();

    $response = $this->actingAs($user)->post(route('logout'));

    $this->assertGuest();
    $response->assertRedirect(route('login'));
});
