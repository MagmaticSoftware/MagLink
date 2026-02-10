<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class ShareTrialData
{
    /**
     * Handle an incoming request.
     * 
     * Condivide i dati del trial e dell'abbonamento con Inertia/Vue.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if ($user) {
            $subscription = $user->subscription('default');
            
            // Determina il nome del piano e la periodicità basandosi sul stripe_price
            $planName = null;
            $planKey = null;
            $billingType = null;
            
            if ($subscription && $subscription->stripe_price) {
                $plans = config('subscriptions.plans', []);
                foreach ($plans as $key => $planData) {
                    // Controlla monthly
                    if (isset($planData['monthly']['price_id']) && $planData['monthly']['price_id'] === $subscription->stripe_price) {
                        $planName = $planData['name'];
                        $planKey = $key;
                        $billingType = 'monthly';
                        break;
                    }
                    // Controlla yearly
                    if (isset($planData['yearly']['price_id']) && $planData['yearly']['price_id'] === $subscription->stripe_price) {
                        $planName = $planData['name'];
                        $planKey = $key;
                        $billingType = 'yearly';
                        break;
                    }
                }
            }
            
            Inertia::share([
                'trial' => [
                    'active' => $user->onTrial(),
                    'expired' => $user->trialExpired(),
                    'days_left' => $user->trialDaysLeft(),
                    'ends_at' => $user->trial_ends_at?->toISOString(),
                ],
                'subscription' => $subscription ? [
                    'name' => $planName,
                    'key' => $planKey,
                    'billing_type' => $billingType,
                    'active' => $subscription->active(),
                    'ends_at' => $subscription->ends_at?->toISOString(),
                    // Usa il campo current_period_end dal database, con fallback all'API Stripe
                    'current_period_end' => $subscription->current_period_end ? 
                        $subscription->current_period_end->toISOString() : 
                        ($subscription->asStripeSubscription()->current_period_end ? 
                            \Carbon\Carbon::createFromTimestamp($subscription->asStripeSubscription()->current_period_end)->toISOString() : null),
                    'created_at' => $subscription->created_at?->toISOString(),
                    'stripe_price' => $subscription->stripe_price,
                    'on_grace_period' => $subscription->onGracePeriod(),
                    'cancelled' => $subscription->canceled(),
                ] : null,
                'plans' => config('subscriptions.plans'),
                'billing' => [
                    'isNewUser' => $user->isNewUser(),
                    'hasActiveTrial' => $user->onTrial(),
                    'canStartTrial' => $user->canStartTrial(),
                    'isSubscribed' => $user->subscribed('default'),
                    'onFreePlan' => $user->onFreePlan(),
                    'currentPlanName' => $user->currentPlanName(),
                    'currentPlanKey' => $user->currentPlanKey(),
                    'hasAccess' => $user->hasAccess(),
                    'limits' => $user->getPlanLimits(),
                    // QR Code customization
                    'plan' => $user->currentPlanKey() ?? 'free',
                    'plan_name' => $user->currentPlanName() ?? 'Free',
                    'qr_customization' => $user->getQrCustomizationOptions(),
                    'on_trial' => $user->onTrial(),
                    'trial_days_left' => $user->trialDaysLeft(),
                ],
            ]);
        } else {
            // Condividi dati vuoti quando l'utente non è autenticato
            Inertia::share([
                'trial' => [
                    'active' => false,
                    'expired' => false,
                    'days_left' => 0,
                    'ends_at' => null,
                ],
                'subscription' => null,
                'plans' => config('subscriptions.plans'),
                'billing' => [
                    'isNewUser' => false,
                    'hasActiveTrial' => false,
                    'canStartTrial' => false,
                    'isSubscribed' => false,
                    'onFreePlan' => false,
                    'currentPlanName' => null,
                    'hasAccess' => false,
                    // QR Code customization defaults for non-authenticated users
                    'plan' => 'free',
                    'plan_name' => 'Free',
                    'qr_customization' => config('subscriptions.plans.free.qr_customization', [
                        'colors' => true,
                        'logo' => false,
                        'remove_background' => false,
                        'max_size' => 512,
                    ]),
                    'on_trial' => false,
                    'trial_days_left' => 0,
                ],
            ]);
        }
        
        return $next($request);
    }
}
