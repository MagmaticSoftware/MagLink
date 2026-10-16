<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\BillingProfile;
use App\Models\Company;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserPreferences;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Honeypot\Honeypot;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register', [
            'honeypot' => app(Honeypot::class)->toArray(),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $isBusiness = $request->input('account_type') === 'business';

        $request->validate([
            'account_type' => 'nullable|in:personal,business',
            'usage_type' => $isBusiness ? 'required|in:company,freelancer,agency' : 'nullable|in:personal',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company_name' => $isBusiness ? 'required|string|max:255' : 'nullable|string|max:255',
            'company_email' => $isBusiness ? 'required|email|max:255' : 'nullable|email|max:255',
            'terms_accepted' => 'accepted',
            'slug' => 'required|string|lowercase|alpha_dash|max:255|unique:tenants,id',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Il privato non ha un'azienda: lo spazio prende il suo nome
        if (! $isBusiness) {
            $request->merge([
                'company_name' => trim($request->first_name.' '.$request->last_name),
                'company_email' => $request->email,
                'usage_type' => 'personal',
            ]);
        }

        $tenant = Tenant::create([
            'id' => $request->slug,
            'name' => $request->company_name,
        ]);

        $user = null;
        
        $tenant->run(function () use ($request, $tenant, &$user) {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Crea la Company
            $company = Company::create([
                'name' => $request->company_name,
                'slug' => $request->slug,
                'email' => $request->company_email,
                'industry' => $request->company_industry,
                'size' => $request->company_size,
            ]);

            $user->companies()->attach($company->id, [
                'is_company_admin' => true,
            ]);

            // Crea il BillingProfile
            BillingProfile::create([
                'company_id' => $company->id,
                'company_name' => $request->company_name,
                'phone' => $request->company_phone,
            ]);

            // Il form invia referral_source/language/terms_accepted/newsletter_opt_in:
            // accettiamo anche i nomi storici delle colonne per retrocompatibilità
            $termsAccepted = $request->boolean('terms_accepted') || $request->boolean('terms_and_conditions');
            $privacyAccepted = $request->boolean('privacy_policy');
            $newsletter = $request->boolean('newsletter_opt_in') || $request->boolean('newsletter');

            UserPreferences::create([
                'user_id' => $user->id,
                'usage_type' => $request->usage_type ?: 'personal',
                'discovery_source' => $request->referral_source ?: ($request->discovery_source ?: 'direct'),
                'main_goal' => $request->main_goal ?: 'manage campaigns',
                'estimated_usage' => $request->estimated_usage ?: 'medium',
                // La dimensione del team non viene più chiesta: usiamo la dimensione aziendale
                'team_size' => $request->team_size ?: ($request->company_size ?: 'solo'),
                'lang' => $request->language ?: ($request->lang ?: 'en'),
                'timezone' => $request->timezone ?: config('app.timezone'),
                'terms_and_conditions' => $termsAccepted,
                'terms_and_conditions_accepted_at' => $termsAccepted ? now() : null,
                'privacy_policy' => $privacyAccepted,
                'privacy_policy_accepted_at' => $privacyAccepted ? now() : null,
                'newsletter' => $newsletter,
                'newsletter_accepted_at' => $newsletter ? now() : null,
            ]);
            
            event(new Registered($user));
        });

        // Login fuori dal contesto tenant
        Auth::login($user);
        
        return redirect()->route('tenant.index', ['tenant' => $tenant->id]);
    }
}
