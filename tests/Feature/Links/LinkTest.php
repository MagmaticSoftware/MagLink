<?php

use App\Models\Company;
use App\Models\Link;
use App\Models\User;
use App\Models\View;
use Inertia\Testing\AssertableInertia as Assert;

function companyFor(User $user): Company
{
    return Company::forceCreate([
        'name' => 'Acme',
        'slug' => $user->tenant_id,
        'tenant_id' => $user->tenant_id,
    ]);
}

function createLinks(User $user, int $count, array $attributes = []): void
{
    $company = Company::where('tenant_id', $user->tenant_id)->first() ?? companyFor($user);

    foreach (range(1, $count) as $i) {
        Link::withoutEvents(fn () => Link::forceCreate(array_merge([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'tenant_id' => $user->tenant_id,
            'slug' => strtolower(uniqid('l')),
            'url' => 'https://example.com',
            'title' => "Link {$i}",
            'is_active' => true,
        ], $attributes)));
    }
}

function storeLink($test, User $user, array $overrides = [])
{
    $company = Company::where('tenant_id', $user->tenant_id)->first() ?? companyFor($user);

    return $test->actingAs($user)->post(route('links.store', ['tenant' => $user->tenant_id]), array_merge([
        'user_id' => $user->id,
        'company_id' => $company->id,
        'slug' => 'nuovo-link',
        'url' => 'https://example.com/landing',
        'title' => 'Nuovo link',
        'is_active' => true,
    ], $overrides));
}

test('free plan users can create links below the limit', function () {
    $user = $this->createTenantUser(['free_plan_started_at' => now()]);
    createLinks($user, 4);

    storeLink($this, $user)->assertRedirect(route('links.index', ['tenant' => $user->tenant_id]));

    expect(Link::where('user_id', $user->id)->count())->toBe(5);
});

test('free plan users can not exceed the link limit', function () {
    $user = $this->createTenantUser(['free_plan_started_at' => now()]);
    createLinks($user, 5);

    storeLink($this, $user)->assertSessionHas('error');

    expect(Link::where('user_id', $user->id)->count())->toBe(5);
});

test('free plan users at the limit are redirected away from the create page', function () {
    $user = $this->createTenantUser(['free_plan_started_at' => now()]);
    createLinks($user, 5);

    $this->actingAs($user)
        ->get(route('links.create', ['tenant' => $user->tenant_id]))
        ->assertRedirect(route('links.index', ['tenant' => $user->tenant_id]));
});

test('pro lifetime users have no link limit', function () {
    $user = $this->createTenantUser([
        'free_plan_started_at' => now()->subMonth(),
        'pro_lifetime_started_at' => now(),
    ]);
    createLinks($user, 10);

    storeLink($this, $user)->assertSessionMissing('error');

    expect(Link::where('user_id', $user->id)->count())->toBe(11);
});

test('link slugs must be unique and well formed', function () {
    $user = $this->createTenantUser(['free_plan_started_at' => now()]);
    createLinks($user, 1, ['slug' => 'gia-usato']);

    storeLink($this, $user, ['slug' => 'gia-usato'])->assertSessionHasErrors('slug');
    storeLink($this, $user, ['slug' => 'Non Valido'])->assertSessionHasErrors('slug');
});

test('users only see the links of their own tenant', function () {
    $user = $this->createTenantUser();
    $other = $this->createTenantUser();
    createLinks($user, 2);
    createLinks($other, 3);

    $this->actingAs($user)
        ->get(route('links.index', ['tenant' => $user->tenant_id]))
        ->assertInertia(fn (Assert $page) => $page->has('links', 2)->etc());
});

test('users can not open the links of another tenant', function () {
    $user = $this->createTenantUser();
    $other = $this->createTenantUser();
    createLinks($other, 1);

    $this->actingAs($user)
        ->get(route('links.index', ['tenant' => $other->tenant_id]))
        ->assertForbidden();
});

test('active short links redirect to their destination and count the view', function () {
    $user = $this->createTenantUser();
    createLinks($user, 1, ['slug' => 'promo', 'url' => 'https://example.com/promo']);

    $this->withHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0 Safari/537.36')
        ->get(route('redirect.slug', ['slug' => 'promo']))
        ->assertRedirect('https://example.com/promo');

    expect(View::count())->toBe(1);
});

test('links that require consent show the consent page first', function () {
    $user = $this->createTenantUser();
    createLinks($user, 1, ['slug' => 'privacy', 'require_consent' => true]);

    $this->withHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0 Safari/537.36')
        ->get(route('redirect.slug', ['slug' => 'privacy']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('ConsentPage'));

    expect(View::count())->toBe(0);
});

test('inactive or unknown short links return 404', function () {
    $user = $this->createTenantUser();
    createLinks($user, 1, ['slug' => 'spento', 'is_active' => false]);

    $this->get(route('redirect.slug', ['slug' => 'spento']))->assertNotFound();
    $this->get(route('redirect.slug', ['slug' => 'inesistente']))->assertNotFound();
});
