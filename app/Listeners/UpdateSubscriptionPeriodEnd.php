<?php

namespace App\Listeners;

use Laravel\Cashier\Events\WebhookReceived;
use Illuminate\Support\Facades\Log;

class UpdateSubscriptionPeriodEnd
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     * 
     * Aggiorna il campo current_period_end nella tabella stripe_subscriptions
     * quando riceviamo eventi da Stripe relativi alle subscription.
     */
    public function handle(WebhookReceived $event): void
    {
        $payload = $event->payload;
        
        // Eventi che ci interessano per aggiornare current_period_end
        $relevantEvents = [
            'customer.subscription.created',
            'customer.subscription.updated',
            'customer.subscription.deleted',
            'invoice.payment_succeeded',
        ];
        
        if (!in_array($payload['type'], $relevantEvents)) {
            return;
        }
        
        try {
            // Ottieni i dati della subscription dal payload
            $subscriptionData = $payload['data']['object'];
            
            // Se è un evento invoice, prendi la subscription dall'invoice
            if ($payload['type'] === 'invoice.payment_succeeded' && isset($subscriptionData['subscription'])) {
                $stripeSubscriptionId = $subscriptionData['subscription'];
                
                // Per gli invoice, il period end potrebbe essere nelle lines
                if (isset($subscriptionData['lines']['data'][0]['period']['end'])) {
                    $currentPeriodEnd = $subscriptionData['lines']['data'][0]['period']['end'];
                } else {
                    $currentPeriodEnd = null;
                }
            } else {
                $stripeSubscriptionId = $subscriptionData['id'] ?? null;
                $currentPeriodEnd = $subscriptionData['current_period_end'] ?? null;
            }
            
            if (!$stripeSubscriptionId || !$currentPeriodEnd) {
                Log::debug('Skipping webhook - missing required data', [
                    'event' => $payload['type'],
                    'has_subscription_id' => !empty($stripeSubscriptionId),
                    'has_period_end' => !empty($currentPeriodEnd),
                ]);
                return;
            }
            
            // Trova la subscription nel database
            $subscription = \App\Models\StripeSubscription::where('stripe_id', $stripeSubscriptionId)->first();
            
            if ($subscription) {
                $subscription->current_period_end = \Carbon\Carbon::createFromTimestamp($currentPeriodEnd);
                $subscription->save();
                
                Log::info('Updated current_period_end for subscription', [
                    'subscription_id' => $stripeSubscriptionId,
                    'current_period_end' => $subscription->current_period_end,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error updating current_period_end', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);
        }
    }
}
