<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StripeSubscription;
use Carbon\Carbon;

class SyncSubscriptionPeriods extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:sync-periods';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincronizza i current_period_end delle subscription attive da Stripe';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sincronizzazione current_period_end delle subscription...');
        
        // Ottieni tutte le subscription attive (anche quelle già sincronizzate per aggiornare i dati)
        // Includiamo anche past_due/unpaid/incomplete: se Stripe ha perso un webhook lo stato locale
        // può essere rimasto indietro, quindi allineiamo stato, prezzo e scadenza da Stripe.
        $subscriptions = StripeSubscription::whereIn('stripe_status', ['active', 'trialing', 'past_due', 'unpaid', 'incomplete'])
            ->orWhere(fn ($q) => $q->where('stripe_status', 'canceled')->whereNull('ends_at'))
            ->get();
        
        if ($subscriptions->isEmpty()) {
            $this->info('Nessuna subscription da sincronizzare.');
            return 0;
        }
        
        $this->info("Trovate {$subscriptions->count()} subscription da sincronizzare.");
        
        $bar = $this->output->createProgressBar($subscriptions->count());
        $bar->start();
        
        $synced = 0;
        $errors = 0;
        $skipped = 0;
        
        foreach ($subscriptions as $subscription) {
            try {
                $stripeSubscription = $subscription->asStripeSubscription();
                
                // Converti l'oggetto Stripe in array usando json_decode/encode
                $data = json_decode(json_encode($stripeSubscription), true);
                
                $dirty = false;

                // Allinea stato, prezzo e data di fine (subscription annullata su Stripe)
                if (($data['status'] ?? null) && $data['status'] !== $subscription->stripe_status) {
                    $subscription->stripe_status = $data['status'];
                    $dirty = true;
                }
                $stripePrice = $data['items']['data'][0]['price']['id'] ?? null;
                if ($stripePrice && $stripePrice !== $subscription->stripe_price) {
                    $subscription->stripe_price = $stripePrice;
                    $dirty = true;
                }
                $endedAt = $data['ended_at'] ?? $data['cancel_at'] ?? null;
                if ($endedAt && !$subscription->ends_at) {
                    $subscription->ends_at = Carbon::createFromTimestamp($endedAt);
                    $dirty = true;
                }
                if ($dirty) {
                    $subscription->save();
                    $synced++;
                }

                // Il current_period_end è negli items, non nella subscription root
                $currentPeriodEnd = $data['items']['data'][0]['current_period_end'] ?? null;
                
                if ($currentPeriodEnd) {
                    $newPeriodEnd = Carbon::createFromTimestamp($currentPeriodEnd);
                    
                    // Aggiorna solo se il valore è cambiato o se era null
                    if (!$subscription->current_period_end || !$subscription->current_period_end->eq($newPeriodEnd)) {
                        $subscription->current_period_end = $newPeriodEnd;
                        $subscription->save();
                        $synced++;
                    } else {
                        $skipped++;
                    }
                } else {
                    $this->warn("\nSubscription {$subscription->stripe_id}: current_period_end non trovato");
                    $skipped++;
                }
            } catch (\Exception $e) {
                $this->error("\nErrore per subscription {$subscription->stripe_id}: {$e->getMessage()}");
                $errors++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("✓ Sincronizzate: {$synced}");
        if ($skipped > 0) {
            $this->info("→ Già aggiornate: {$skipped}");
        }
        if ($errors > 0) {
            $this->warn("✗ Errori: {$errors}");
        }
        
        return 0;
    }
}
