<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Cashier\SubscriptionItem as CashierSubscriptionItem;

class StripeSubscriptionItem extends CashierSubscriptionItem
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stripe_subscription_items';

    /**
     * Get the subscription that the item belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\StripeSubscription, $this>
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(StripeSubscription::class, 'stripe_subscription_id');
    }
}
