<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
    public function handleFreePlan(Request $request)
    {
        $user = $request->user();
        
        // Se già abbonato a un piano a pagamento, non può passare al piano gratuito tramite checkout
        if ($user->subscribed('default') && !$user->onFreePlan()) {
            return redirect()->route('plans.index', ['tenant' => $user->tenant_id])
                ->with('info', 'Hai già un abbonamento attivo. Per passare al piano gratuito, contatta il supporto.');
        }
        
        // Se ha già il piano gratuito, reindirizza alla dashboard
        if ($user->onFreePlan()) {
            return \Inertia\Inertia::location(route('tenant.index', ['tenant' => $user->tenant_id]));
        }
        
        // Per il piano gratuito, "bruciamo" il trial
        $user->burnTrial();
        
        // Segna l'utente come su piano free aggiungendo un metadato
        // Invece di creare un abbonamento Stripe, gestiamo il piano free internamente
        $user->free_plan_started_at = now();
        $user->save();
        
        // Usa Inertia::location per forzare un reload completo della pagina
        return \Inertia\Inertia::location(route('tenant.index', ['tenant' => $user->tenant_id]));
    }

    /**
     * Successfully completed the checkout process.
     */
    public function success(Request $request)
    {
        $user = $request->user();
        $sessionId = $request->get('session_id');
        
        if (!$sessionId) {
            return redirect()->route('plans.index', ['tenant' => $user->tenant_id])
                ->with('error', 'Sessione di checkout non valida.');
        }

        try {
            // Recupera i dettagli della sessione da Stripe
            $stripe = new \Stripe\StripeClient(config('cashier.secret'));
            $session = $stripe->checkout->sessions->retrieve($sessionId, [
                'expand' => ['subscription', 'customer']
            ]);

            // Verifica che la sessione sia stata completata con successo
            if ($session->status !== 'complete' || $session->payment_status !== 'paid') {
                return redirect()->route('plans.index', ['tenant' => $user->tenant_id])
                    ->with('error', 'Il pagamento non è stato completato.');
            }

            // Se l'utente non ha ancora un customer ID Stripe, impostalo
            if (!$user->hasStripeId() && $session->customer) {
                $user->createOrGetStripeCustomer([
                    'id' => $session->customer->id ?? $session->customer,
                ]);
            }

            // Se la subscription è presente nella sessione, creala/aggiornala nel database
            if ($session->subscription) {
                $subscriptionId = is_string($session->subscription) ? $session->subscription : $session->subscription->id;
                
                // Verifica se la subscription esiste già nel database
                $existingSubscription = $user->subscriptions()
                    ->where('stripe_id', $subscriptionId)
                    ->first();

                if (!$existingSubscription) {
                    // Recupera i dettagli completi della subscription da Stripe
                    $stripeSubscription = $stripe->subscriptions->retrieve($subscriptionId);
                    
                    // Crea manualmente la subscription nel database
                    $user->subscriptions()->create([
                        'type' => 'default',
                        'stripe_id' => $subscriptionId,
                        'stripe_status' => $stripeSubscription->status,
                        'stripe_price' => $stripeSubscription->items->data[0]->price->id ?? null,
                        'quantity' => $stripeSubscription->items->data[0]->quantity ?? 1,
                        'trial_ends_at' => null,
                        'ends_at' => null,
                    ]);
                }
            }

            // "Brucia" il diritto al trial se non è stato ancora utilizzato
            $user->burnTrial();
            
            // Refresh del modello per ottenere i dati aggiornati
            $user->refresh();
            
            // Usa Inertia::location per forzare un reload completo della pagina
            return \Inertia\Inertia::location(route('tenant.index', ['tenant' => $user->tenant_id]));
            
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe checkout success error: ' . $e->getMessage());
            
            return redirect()->route('plans.index', ['tenant' => $user->tenant_id])
                ->with('error', 'Errore durante la verifica del pagamento. Contatta il supporto se il problema persiste.');
        } catch (\Exception $e) {
            Log::error('Checkout success error: ' . $e->getMessage());
            
            return redirect()->route('plans.index', ['tenant' => $user->tenant_id])
                ->with('error', 'Errore imprevisto. Contatta il supporto se il problema persiste.');
        }
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
