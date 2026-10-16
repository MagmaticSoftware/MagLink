<?php

namespace Tests;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // I test non dipendono dalla build degli asset frontend
        $this->withoutVite();

        // Le pagine Inertia stanno in resources/js/pages (minuscolo), non nel default "Pages"
        config(['inertia.testing.page_paths' => [resource_path('js/pages')]]);
    }

    /**
     * Crea un utente con il relativo tenant, come avviene in fase di registrazione.
     */
    protected function createTenantUser(array $attributes = [], ?string $tenantId = null): User
    {
        $tenant = Tenant::create([
            'id' => $tenantId ?? 'tenant-'.uniqid(),
            'name' => 'Test Tenant',
        ]);

        return User::factory()->create(array_merge(['tenant_id' => $tenant->id], $attributes));
    }

    /**
     * Associa all'utente una subscription Stripe locale, senza chiamare le API di Stripe.
     */
    protected function subscribeTo(User $user, string $plan, string $interval = 'monthly', string $status = 'active', array $attributes = []): void
    {
        $user->subscriptions()->create(array_merge([
            'type' => 'default',
            'stripe_id' => 'sub_'.uniqid(),
            'stripe_status' => $status,
            'stripe_price' => config("subscriptions.plans.{$plan}.{$interval}.price_id"),
            'quantity' => 1,
            'current_period_end' => now()->addMonth(),
        ], $attributes));
    }
}
