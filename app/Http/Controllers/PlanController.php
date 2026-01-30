<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class PlanController extends Controller
{
    /**
     * Display a listing of the subscription plans.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $plans = config('subscriptions.plans');
        
        // Determina il piano attivo corrente
        $currentPlanKey = null;
        $subscriptionEndsAt = null;
        $onFreePlan = $user && $user->onFreePlan();
        
        if ($user) {
            $currentPlanKey = $user->currentPlanKey();
            
            // Se ha una subscription Stripe attiva, prendi la data di scadenza
            if ($user->subscribed('default')) {
                $subscription = $user->subscription('default');
                $subscriptionEndsAt = $subscription->ends_at ?? $subscription->asStripeSubscription()->current_period_end;
            }
            // Se è in trial, prendi la data di scadenza del trial
            elseif ($user->onTrial()) {
                $subscriptionEndsAt = $user->trial_ends_at;
            }
        }
        
        return Inertia::render('billing/Plans', [
            'plans' => $plans,
            'isNewUser' => $user ? $user->isNewUser() : false,
            'hasActiveTrial' => $user ? $user->onTrial() : false,
            'canStartTrial' => $user ? $user->canStartTrial() : false,
            'currentPlan' => $user ? $user->currentPlanName() : null,
            'currentPlanKey' => $currentPlanKey,
            'isSubscribed' => $user ? $user->subscribed('default') : false,
            'onFreePlan' => $onFreePlan,
            'subscriptionEndsAt' => $subscriptionEndsAt,
            'trialEndsAt' => $user ? $user->trial_ends_at : null,
        ]);
    }

    /**
     * Avvia il trial gratuito di 30 giorni per l'utente
     */
    public function startTrial(Request $request)
    {
        $user = $request->user();
        
        // Avvia il trial solo se l'utente può iniziare un trial
        if ($user->canStartTrial()) {
            $user->startTrial();
            
            // Refresh del modello per essere sicuri dei dati aggiornati
            $user->refresh();
            
            return Inertia::location(route('tenant.index', ['tenant' => $user->tenant_id]));
        }
        
        return redirect()->route('plans.index', ['tenant' => $user->tenant_id])
            ->with('error', 'Hai già utilizzato il tuo trial gratuito. Scegli un piano per continuare.');
    }

    /**
     * Display the specified plan details.
     */
    public function show(string $plan)
    {
        $plans = config('subscriptions.plans');
        
        if (!isset($plans[$plan])) {
            abort(404, 'Piano non trovato');
        }
        
        return Inertia::render('billing/PlanDetail', [
            'plan' => $plans[$plan],
            'planKey' => $plan,
        ]);
    }
}
