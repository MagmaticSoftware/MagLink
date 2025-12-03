<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, BelongsToTenant, HasApiTokens, SoftDeletes, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'trial_ends_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'trial_ends_at' => 'datetime',
        ];
    }

    /**
     * Get the tenant associated with the user.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the links associated with the user.
     */
    public function links()
    {
        return $this->hasMany(Link::class);
    }

    /**
     * Get the company associated with the user.
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }

    /**
     * Override subscriptions relation to use custom model with custom table
     */
    public function subscriptions()
    {
        return $this->hasMany(StripeSubscription::class, 'user_id')->orderBy('created_at', 'desc');
    }

    // ==========================================
    // Trial Methods
    // ==========================================

    /**
     * Avvia il trial di 30 giorni per l'utente
     */
    public function startTrial(): void
    {
        if (!$this->trial_ends_at) {
            $this->update([
                'trial_ends_at' => now()->addDays(30)
            ]);
        }
    }

    /**
     * Verifica se l'utente può iniziare un trial
     */
    public function canStartTrial(): bool
    {
        // Può iniziare trial se non ha mai avuto un trial (trial_ends_at è null)
        return is_null($this->trial_ends_at);
    }

    /**
     * "Brucia" il diritto al trial quando l'utente acquista un piano
     * Imposta trial_ends_at nel passato per prevenire uso futuro del trial
     */
    public function burnTrial(): void
    {
        if (is_null($this->trial_ends_at)) {
            $this->update([
                'trial_ends_at' => now()->subDay() // Un giorno nel passato
            ]);
        }
    }

    /**
     * Verifica se l'utente è in trial (trial attivo)
     */
    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Verifica se il trial è scaduto
     */
    public function trialExpired(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isPast();
    }

    /**
     * Ottiene i giorni rimanenti del trial
     */
    public function trialDaysLeft(): int
    {
        if (!$this->onTrial()) {
            return 0;
        }
        
        return (int) now()->diffInDays($this->trial_ends_at, false);
    }

    /**
     * Verifica se è un nuovo utente (registrato negli ultimi 7 giorni)
     */
    public function isNewUser(): bool
    {
        return $this->created_at && $this->created_at->isAfter(now()->subDays(7));
    }

    /**
     * Verifica se l'utente ha accesso all'app (trial attivo, abbonato, o piano gratuito)
     */
    public function hasAccess(): bool
    {
        // Ha un abbonamento attivo
        if ($this->subscribed('default')) {
            return true;
        }

        // È in trial
        if ($this->onTrial()) {
            return true;
        }

        // Può ancora iniziare un trial (nuovo utente)
        if ($this->canStartTrial()) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se l'utente è sul piano gratuito
     */
    public function onFreePlan(): bool
    {
        $subscription = $this->subscription('default');
        
        if (!$subscription) {
            return false;
        }

        $freePriceIds = config('subscriptions.plans.free.monthly.price_id', []);
        
        return in_array($subscription->stripe_price, (array) $freePriceIds);
    }

    /**
     * Ottiene il nome del piano corrente
     */
    public function currentPlanName(): ?string
    {
        $subscription = $this->subscription('default');
        
        if (!$subscription) {
            return null;
        }

        $plans = config('subscriptions.plans', []);
        
        foreach ($plans as $planKey => $planData) {
            if (isset($planData['monthly']['price_id']) && $planData['monthly']['price_id'] === $subscription->stripe_price) {
                return $planData['name'];
            }
            if (isset($planData['yearly']['price_id']) && $planData['yearly']['price_id'] === $subscription->stripe_price) {
                return $planData['name'];
            }
        }

        return null;
    }
}