<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::findOrCreate('tenant', 'web');

        $tenant1 = Tenant::firstOrCreate(['id' => 'demo'], ['name' => 'Demo Tenant']);
        $tenant1->run(function () {
            $user = User::factory()->create([
                'first_name' => 'Demo',
                'last_name' => 'User',
                'email' => 'demo@demo.com',
            ]);

            $user->assignRole('tenant');

            $company = Company::factory()->create([
                'name' => 'Demo Company',
                'slug' => 'demo',
            ]);

            $company->users()->attach($user->id, ['is_company_admin' => true]);
        });

        $tenant2 = Tenant::firstOrCreate(['id' => 'maglink'], ['name' => 'Maglink']);
        $tenant2->run(function () {
            $user = User::factory()->create([
                'first_name' => 'Maglink',
                'last_name' => 'User',
                'email' => 'mag@demo.com',
            ]);

            $user->assignRole('tenant');

            $company = Company::factory()->create([
                'name' => 'Maglink Company',
                'slug' => 'maglink',
            ]);

            $company->users()->attach($user->id, ['is_company_admin' => true]);
        });

        $admin = User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@demo.com',
        ]);

        Role::findOrCreate('admin', 'web');

        $admin->assignRole('admin');

        $this->call([
            LinkSeeder::class,
            QrCodeSeeder::class,
            PageSeeder::class,
            PageBlockSeeder::class,
        ]);
    }
}
