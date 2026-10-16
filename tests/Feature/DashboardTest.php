<?php

test('guests are redirected to the login page', function () {
    $user = $this->createTenantUser();

    $this->get(route('tenant.index', ['tenant' => $user->tenant_id]))
        ->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = $this->createTenantUser();

    $this->actingAs($user)
        ->get(route('tenant.index', ['tenant' => $user->tenant_id]))
        ->assertOk();
});

function dashboardLink(\App\Models\User $user, string $slug, $createdAt): \App\Models\Link
{
    $company = \App\Models\Company::firstOrCreate(
        ['slug' => $user->tenant_id],
        ['name' => 'Acme', 'tenant_id' => $user->tenant_id],
    );

    return \App\Models\Link::withoutEvents(fn () => \App\Models\Link::forceCreate([
        'user_id' => $user->id, 'company_id' => $company->id, 'tenant_id' => $user->tenant_id,
        'slug' => $slug, 'url' => 'https://example.com', 'title' => $slug, 'is_active' => true,
        'created_at' => $createdAt, 'updated_at' => $createdAt,
    ]));
}

test('dashboard trends compare the period with the previous one', function () {
    $user = $this->createTenantUser();
    // 3 link negli ultimi 7 giorni, 2 nei 7 precedenti, 1 più vecchio
    dashboardLink($user, 'oggi', now());
    dashboardLink($user, 'ieri', now()->subDay());
    dashboardLink($user, 'sei-giorni', now()->subDays(6));
    dashboardLink($user, 'otto-giorni', now()->subDays(8));
    dashboardLink($user, 'tredici-giorni', now()->subDays(13));
    dashboardLink($user, 'vecchio', now()->subDays(40));

    $this->actingAs($user)
        ->get(route('tenant.index', ['tenant' => $user->tenant_id]))
        ->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
            ->where('period', 7)
            ->has('performance', 7)
            ->has('trends.links.series', 7)
            ->where('trends.links.current', 3)
            ->where('trends.links.previous', 2)
            ->where('trends.links.change', 50)
            ->where('trends.links.series.6', 1)
            ->where('trends.qrcodes.change', null)
            ->where('latest_links.0.views_count', 0)
            ->etc()
        );
});

test('dashboard supports a 30 day period and ignores invalid ones', function () {
    $user = $this->createTenantUser();

    $this->actingAs($user)
        ->get(route('tenant.index', ['tenant' => $user->tenant_id, 'days' => 30]))
        ->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
            ->where('period', 30)
            ->has('performance', 30)
            ->has('trends.views.series', 30)
            ->etc()
        );

    $this->actingAs($user)
        ->get(route('tenant.index', ['tenant' => $user->tenant_id, 'days' => 999]))
        ->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page->where('period', 7)->etc());
});

test('dashboard ranks the top links by clicks in the period', function () {
    $user = $this->createTenantUser();
    $popular = dashboardLink($user, 'popolare', now()->subDays(20));
    $quiet = dashboardLink($user, 'tranquillo', now()->subDays(20));
    dashboardLink($user, 'senza-click', now()->subDays(20));

    $view = fn ($link, $at) => \App\Models\View::create(['viewable_type' => \App\Models\Link::class, 'viewable_id' => $link->id, 'consent_given' => false])
        ->forceFill(['created_at' => $at])->save();
    foreach (range(1, 4) as $i) { $view($popular, now()->subDays(1)); }
    foreach (range(1, 2) as $i) { $view($popular, now()->subDays(9)); }
    $view($quiet, now());

    $this->actingAs($user)
        ->get(route('tenant.index', ['tenant' => $user->tenant_id]))
        ->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
            ->has('top_links', 2)
            ->where('top_links.0.slug', 'popolare')
            ->where('top_links.0.views', 4)
            ->where('top_links.0.change', 100)
            ->where('top_links.1.slug', 'tranquillo')
            ->where('top_links.1.change', null)
            ->etc()
        );
});

test('help and menu pages are available to tenant users', function () {
    $user = $this->createTenantUser();

    $this->actingAs($user)->get(route('help.index', ['tenant' => $user->tenant_id]))
        ->assertOk()->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page->component('tenant/help/Index'));
    $this->actingAs($user)->get(route('menu.index', ['tenant' => $user->tenant_id]))
        ->assertOk()->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page->component('tenant/menu/ComingSoon'));
});

test('the help page is readable even without an active plan', function () {
    $user = $this->createTenantUser(['trial_ends_at' => now()->subDay()]);

    $this->actingAs($user)->get(route('help.index', ['tenant' => $user->tenant_id]))->assertOk();
});

test('link and qr code lists include 7 day trends and sparklines', function () {
    $user = $this->createTenantUser();
    $link = dashboardLink($user, 'trend', now()->subDays(2));

    $this->actingAs($user)->get(route('links.index', ['tenant' => $user->tenant_id]))
        ->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
            ->has('trends.created.series', 7)
            ->where('trends.created.current', 1)
            ->has("sparklines.{$link->id}", 7)
            ->etc());

    $this->actingAs($user)->get(route('qrcodes.index', ['tenant' => $user->tenant_id]))
        ->assertOk()->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page->has('trends.views.series', 7)->etc());
});
