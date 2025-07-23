<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\BillingProfile;
use App\Models\Company;
use App\Models\Tenant;
use App\Models\User;
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
            'slug' => 'required|string|lowercase|alpha_dash|max:255|unique:tenants,id',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $tenant = Tenant::create([
            'id' => $request->slug,
            'name' => $request->company_name,
        ]);

        $tenant->run(function () use ($request, $tenant) {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $company = Company::create([
                'name' => $request->company_name,
                'slug' => $request->slug,
                'email' => $request->company_email,
            ]);

            $user->companies()->attach($company->id, [
                'is_company_admin' => true,
            ]);

            BillingProfile::create([
                'company_id' => $company->id,
                'company_name' => $request->company_name,
            ]);
            
            event(new Registered($user));
            
            Auth::login($user);
            return to_route('tenant.index', ['tenant' => $tenant->id]);
        });

    }
}
