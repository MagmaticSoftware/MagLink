<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Subscription as CashierSubscription;

class StripeSubscription extends CashierSubscription
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stripe_subscriptions';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'current_period_end' => 'datetime',
        'trial_ends_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * Get the subscription items related to the subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\StripeSubscriptionItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(StripeSubscriptionItem::class, 'stripe_subscription_id');
    }

    /**
     * Fine del periodo di fatturazione corrente.
     *
     * Usa il valore salvato nel database e, se manca, lo recupera da Stripe e lo salva.
     * Un errore di Stripe (subscription inesistente, chiavi errate, API non raggiungibile)
     * non deve mai bloccare la pagina: viene loggato e si restituisce null, senza
     * ritentare la chiamata per qualche minuto.
     */
    public function periodEnd(): ?Carbon
    {
        if ($this->current_period_end) {
            return $this->current_period_end;
        }

        $failureKey = "stripe-subscription-period-end-failed:{$this->id}";

        // Cache::store() evita i tag che la tenancy aggiunge alla facade,
        // non supportati dallo store "database"
        if (Cache::store()->has($failureKey)) {
            return null;
        }

        try {
            $stripeSubscription = $this->asStripeSubscription();
        } catch (\Throwable $e) {
            Log::warning('Impossibile recuperare la subscription da Stripe', [
                'subscription_id' => $this->id,
                'stripe_id' => $this->stripe_id,
                'user_id' => $this->user_id,
                'error' => $e->getMessage(),
            ]);

            Cache::store()->put($failureKey, true, now()->addMinutes(10));

            return null;
        }

        // Nelle versioni recenti dell'API Stripe il periodo è sugli item della subscription
        $timestamp = $stripeSubscription->current_period_end
            ?? $stripeSubscription->items->data[0]->current_period_end
            ?? null;

        if (! $timestamp) {
            return null;
        }

        $this->forceFill(['current_period_end' => Carbon::createFromTimestamp($timestamp)])->saveQuietly();

        return $this->current_period_end;
    }
}
