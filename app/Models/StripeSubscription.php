<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
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
}
