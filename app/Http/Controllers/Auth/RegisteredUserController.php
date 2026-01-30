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

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'slug' => 'required|string|lowercase|alpha_dash|max:255|unique:tenants,id',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

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

            UserPreferences::create([
                'user_id' => $user->id,
                'usage_type' => $request->usage_type ?? 'personal',
                'discovery_source' => $request->discovery_source ?? 'direct',
                'main_goal' => $request->main_goal ?? 'manage campaigns',
                'estimated_usage' => $request->estimated_usage ?? 'medium',
                'team_size' => $request->team_size ?? 'solo',
                'lang' => $request->lang ?? 'en',
                'timezone' => $request->timezone ?? config('app.timezone'),
                'terms_and_conditions' => $request->terms_and_conditions ?? false,
                'terms_and_conditions_accepted_at' => $request->terms_and_conditions ? now() : null,
                'privacy_policy' => $request->privacy_policy ?? false,
                'privacy_policy_accepted_at' => $request->privacy_policy ? now() : null,
                'newsletter' => $request->newsletter ?? false,
                'newsletter_accepted_at' => $request->newsletter ? now() : null,
            ]);
            
            event(new Registered($user));
        });

        // Login fuori dal contesto tenant
        Auth::login($user);
        
        return redirect()->route('tenant.index', ['tenant' => $tenant->id]);
    }
}
