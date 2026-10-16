<?php

use App\Models\Company;
use App\Models\Link;
use App\Models\Page;
use App\Models\QrCode;
use App\Models\User;

function searchFixtures(User $user): void
{
    $company = Company::forceCreate(['name' => 'Acme', 'slug' => $user->tenant_id, 'tenant_id' => $user->tenant_id]);
    $base = ['user_id' => $user->id, 'company_id' => $company->id, 'tenant_id' => $user->tenant_id, 'is_active' => true];

    Link::forceCreate($base + ['slug' => 'promo-'.$user->id, 'url' => 'https://example.com', 'title' => 'Campagna Autunno', 'description' => 'Saldi stagionali']);
    QrCode::forceCreate($base + ['slug' => 'qr-'.$user->id, 'name' => 'Menu autunno', 'type' => 'static', 'payload' => '{}']);
    Page::forceCreate($base + ['slug' => 'pagina-'.$user->id, 'title' => 'Landing autunno']);
}

function searchFor($test, User $user, string $query)
{
    return $test->actingAs($user)->getJson(route('search', ['tenant' => $user->tenant_id, 'q' => $query]));
}

test('search returns links, qr codes and pages of the current tenant', function () {
    $user = $this->createTenantUser();
    searchFixtures($user);

    searchFor($this, $user, 'autunno')
        ->assertOk()
        ->assertJsonPath('groups.0.key', 'links')
        ->assertJsonPath('groups.0.items.0.title', 'Campagna Autunno')
        ->assertJsonPath('groups.1.key', 'qrcodes')
        ->assertJsonPath('groups.1.items.0.title', 'Menu autunno')
        ->assertJsonPath('groups.2.key', 'pages')
        ->assertJsonPath('groups.2.items.0.title', 'Landing autunno');
});

test('search matches descriptions and slugs', function () {
    $user = $this->createTenantUser();
    searchFixtures($user);

    searchFor($this, $user, 'stagionali')->assertJsonPath('groups.0.items.0.title', 'Campagna Autunno');
    searchFor($this, $user, 'promo')->assertJsonPath('groups.0.items.0.title', 'Campagna Autunno');
});

test('search never returns results from other tenants', function () {
    $user = $this->createTenantUser();
    $other = $this->createTenantUser();
    searchFixtures($other);

    searchFor($this, $user, 'autunno')->assertOk()->assertJsonPath('groups', []);
});

test('search ignores queries that are too short', function () {
    $user = $this->createTenantUser();
    searchFixtures($user);

    searchFor($this, $user, 'a')->assertOk()->assertJsonPath('groups', []);
});

test('guests can not search', function () {
    $user = $this->createTenantUser();

    $this->getJson(route('search', ['tenant' => $user->tenant_id, 'q' => 'autunno']))->assertUnauthorized();
});

test('search results link to the edit pages', function () {
    $user = $this->createTenantUser();
    searchFixtures($user);

    $response = searchFor($this, $user, 'autunno');

    expect($response->json('groups.0.items.0.href'))->toEndWith('/links/'.Link::first()->id.'/edit')
        ->and($response->json('groups.1.items.0.href'))->toEndWith('/qrcodes/qr-'.$user->id.'/edit')
        ->and($response->json('groups.2.items.0.href'))->toEndWith('/pages/pagina-'.$user->id.'/edit');
});
