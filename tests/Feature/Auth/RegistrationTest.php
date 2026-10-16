<?php

use App\Models\Company;
use App\Models\Tenant;
use App\Models\User;
use Spatie\Honeypot\ProtectAgainstSpam;

beforeEach(function () {
    $this->withoutMiddleware(ProtectAgainstSpam::class);
});

function registrationData(array $overrides = []): array
{
    return array_merge([
        'account_type' => 'personal',
        'first_name' => 'Mario',
        'last_name' => 'Rossi',
        'slug' => 'mario-rossi',
        'email' => 'mario@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'terms_accepted' => true,
    ], $overrides);
}

test('registration screen can be rendered', function () {
    $this->get(route('register'))->assertOk();
});

test('new users can register and get their own tenant', function () {
    $response = $this->post(route('register'), registrationData());

    $user = User::where('email', 'mario@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->tenant_id)->toBe('mario-rossi')
        ->and(Tenant::find('mario-rossi'))->not->toBeNull()
        ->and($user->companies()->count())->toBe(1)
        ->and($user->preferences)->not->toBeNull();

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('tenant.index', ['tenant' => 'mario-rossi']));
});

test('business accounts store the company data', function () {
    $this->post(route('register'), registrationData([
        'account_type' => 'business',
        'usage_type' => 'company',
        'company_name' => 'Acme Srl',
        'company_email' => 'info@acme.test',
    ]))->assertSessionHasNoErrors();

    expect(Company::where('slug', 'mario-rossi')->value('name'))->toBe('Acme Srl');
});

test('registration requires terms acceptance', function () {
    $this->post(route('register'), registrationData(['terms_accepted' => false]))
        ->assertSessionHasErrors('terms_accepted');

    $this->assertGuest();
});

test('registration rejects a slug already used by another tenant', function () {
    $this->createTenantUser(tenantId: 'mario-rossi');

    $this->post(route('register'), registrationData())
        ->assertSessionHasErrors('slug');
});
