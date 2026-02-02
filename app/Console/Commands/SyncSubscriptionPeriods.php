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
        $subscriptions = StripeSubscription::whereIn('stripe_status', ['active', 'trialing'])
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
                
                if (isset($stripeSubscription->current_period_end)) {
                    $newPeriodEnd = Carbon::createFromTimestamp($stripeSubscription->current_period_end);
                    
                    // Aggiorna solo se il valore è cambiato o se era null
                    if (!$subscription->current_period_end || !$subscription->current_period_end->eq($newPeriodEnd)) {
                        $subscription->current_period_end = $newPeriodEnd;
                        $subscription->save();
                        $synced++;
                    } else {
                        $skipped++;
                    }
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
