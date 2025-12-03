<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    /**
     * Redirect to Stripe Checkout for subscription.
     */
    public function index(Request $request)
    {
        // Validazione dei parametri
        $request->validate([
            'plan' => 'required|string|in:free,professional,enterprise',
            'billing' => 'required|string|in:monthly,yearly',
        ]);
        
        $planName = $request->plan;
        $billingType = $request->billing;
        
        $plans = config('subscriptions.plans');
        
        // Verifica che il piano esista
        if (!isset($plans[$planName])) {
            abort(404, 'Piano non trovato');
        }
        
        $plan = $plans[$planName];
        
        // Verifica che il tipo di fatturazione esista
        if (!isset($plan[$billingType])) {
            // Se il piano non ha l'opzione yearly (es. free), usa monthly
            $billingType = 'monthly';
        }
        
        // Per il piano gratuito, gestisci diversamente
        if ($planName === 'free') {
            return $this->handleFreePlan($request);
        }
        
        $priceId = $plan[$billingType]['price_id'];
        $user = $request->user();

        try {
            // Debug: controlliamo se l'utente ha già un abbonamento
            if ($user->subscribed('default')) {
                return redirect()->route('plans.index')
                    ->with('error', 'Hai già un abbonamento attivo. Per cambiare piano, vai nelle impostazioni di fatturazione.');
            }
            
            $checkout = $user->newSubscription('default', $priceId)
                ->checkout([
                    'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('checkout.cancel'),
                    'allow_promotion_codes' => true,
                    'billing_address_collection' => 'required',
                    'customer_update' => [
                        'address' => 'auto'
                    ],
                ]);

            return $checkout;
        } catch (\Exception $e) {
            // In caso di errore, torna alla pagina dei piani con messaggio di errore
            return redirect()->route('plans.index')
                ->with('error', 'Errore durante la creazione del checkout: ' . $e->getMessage());
        }
    }

    /**
     * Handle free plan subscription (no Stripe checkout needed).
     */
    protected function handleFreePlan(Request $request)
    {
        $user = $request->user();
        
        // Se già abbonato, non può passare al piano gratuito tramite checkout
        if ($user->subscribed('default')) {
            return redirect()->route('plans.index')
                ->with('info', 'Hai già un abbonamento attivo. Per passare al piano gratuito, contatta il supporto.');
        }
        
        // Per il piano gratuito, "bruciamo" il trial e attiviamo l'account
        $user->burnTrial();
        
        return redirect()->route('tenant.index', ['tenant' => $user->tenant_id])
            ->with('success', 'Piano Free attivato! Puoi passare a un piano a pagamento in qualsiasi momento.');
    }

    /**
     * Successfully completed the checkout process.
     */
    public function success(Request $request)
    {
        $user = $request->user();
        
        // Verifica che l'abbonamento sia effettivamente attivo
        if ($user->subscribed('default')) {
            // "Brucia" il diritto al trial se non è stato ancora utilizzato
            $user->burnTrial();
        }
        
        return redirect()->route('tenant.index', ['tenant' => $user->tenant_id])
            ->with('success', 'Abbonamento attivato con successo!');
    }

    /**
     * Cancel the checkout process.
     */
    public function cancel(Request $request)
    {
        return redirect()->route('plans.index')
            ->with('info', 'Checkout annullato. Puoi riprovare quando vuoi.');
    }

    /**
     * Manage billing portal (per gestire abbonamento esistente).
     */
    public function billingPortal(Request $request)
    {
        $user = $request->user();
        
        if (!$user->hasStripeId()) {
            return redirect()->route('plans.index')
                ->with('error', 'Non hai ancora un account di fatturazione attivo.');
        }
        
        return $user->redirectToBillingPortal(route('tenant.index', ['tenant' => $user->tenant_id]));
    }
}
