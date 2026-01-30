<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckSubscriptionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:check {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check subscription status for a user by email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }
        
        $this->info("User: {$user->first_name} {$user->last_name} ({$user->email})");
        $this->info("Created: {$user->created_at}");
        $this->newLine();
        
        // Trial status
        $this->info('Trial Status:');
        $this->line("  Can start trial: " . ($user->canStartTrial() ? 'Yes' : 'No'));
        $this->line("  On trial: " . ($user->onTrial() ? 'Yes' : 'No'));
        $this->line("  Trial ends at: " . ($user->trial_ends_at ?? 'N/A'));
        $this->newLine();
        
        // Free plan status
        $this->info('Free Plan:');
        $this->line("  On free plan: " . ($user->onFreePlan() ? 'Yes' : 'No'));
        $this->line("  Free plan started at: " . ($user->free_plan_started_at ?? 'N/A'));
        $this->newLine();
        
        // Stripe subscription
        $this->info('Stripe Subscription:');
        $this->line("  Has Stripe ID: " . ($user->hasStripeId() ? 'Yes' : 'No'));
        $this->line("  Stripe ID: " . ($user->stripe_id ?? 'N/A'));
        $this->line("  Subscribed: " . ($user->subscribed('default') ? 'Yes' : 'No'));
        
        $subscription = $user->subscription('default');
        if ($subscription) {
            $this->line("  Subscription ID: {$subscription->stripe_id}");
            $this->line("  Status: {$subscription->stripe_status}");
            $this->line("  Price ID: {$subscription->stripe_price}");
            $this->line("  Created: {$subscription->created_at}");
            $this->line("  Ends at: " . ($subscription->ends_at ?? 'Active (no end date)'));
        }
        $this->newLine();
        
        // Current plan
        $this->info('Current Plan:');
        $this->line("  Plan key: " . ($user->currentPlanKey() ?? 'N/A'));
        $this->line("  Plan name: " . ($user->currentPlanName() ?? 'N/A'));
        $this->newLine();
        
        // Access
        $this->info('Access:');
        $this->line("  Has access: " . ($user->hasAccess() ? 'Yes' : 'No'));
        
        return 0;
    }
}
