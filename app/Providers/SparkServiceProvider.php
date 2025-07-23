<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Spark\Plan;
use Spark\Spark;

class SparkServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Spark::billable(Company::class)->resolve(function (Request $request) {
            return $request->user()->companies()
                       ->wherePivot('is_company_admin', true)
                       ->first();
        });

        Spark::billable(Company::class)->authorize(function (Company $billable, Request $request) {
            return $request->user() &&
                   $request->user()->companies()
                       ->wherePivot('is_company_admin', true)
                       ->where('companies.id', $billable->id)
                       ->exists();
        });

        Spark::billable(Company::class)->checkPlanEligibility(function (Company $billable, Plan $plan) {
            // 
        });
    }
}
