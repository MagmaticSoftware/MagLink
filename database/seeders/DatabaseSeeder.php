<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create essential roles for all environments
        $this->createRolesAndPermissions();

        if (config('app.env') === 'production') {
            // Production: only superadmin, no demo data
            $this->createSuperadmin();
        } else {
            // Local/Development: create demo data
            $this->createDemoData();
        }
    }

    /**
     * Create roles and permissions
     */
    private function createRolesAndPermissions(): void
    {
        $this->command->info('Creating roles and permissions...');

        // Define permissions
        $permissions = [
            'manage tenants', 'view tenants', 'create tenants', 'edit tenants', 'delete tenants',
            'manage users', 'view users', 'create users', 'edit users', 'delete users',
            'manage subscriptions', 'view billing',
            'manage settings', 'view settings',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // Create roles
        $superadmin = Role::findOrCreate('superadmin', 'web');
        $superadmin->givePermissionTo(Permission::all());

        $admin = Role::findOrCreate('admin', 'web');
        $admin->givePermissionTo([
            'manage tenants', 'view tenants', 'create tenants', 'edit tenants',
            'manage users', 'view users', 'create users', 'edit users',
            'manage subscriptions', 'view billing', 'view settings',
        ]);

        Role::findOrCreate('tenant', 'web');

        $this->command->info('✅ Roles and permissions created');
    }

    /**
     * Create superadmin user for production
     */
    private function createSuperadmin(): void
    {
        $email = env('SUPERADMIN_EMAIL', 'admin@maglink.it');

        if (User::where('email', $email)->exists()) {
            $this->command->warn("Superadmin already exists: {$email}");
            return;
        }

        $superadmin = User::create([
            'first_name' => env('SUPERADMIN_FIRST_NAME', 'Super'),
            'last_name' => env('SUPERADMIN_LAST_NAME', 'Admin'),
            'email' => $email,
            'password' => Hash::make(env('SUPERADMIN_PASSWORD', 'password')),
            'email_verified_at' => now(),
        ]);

        $superadmin->assignRole('superadmin');

        $this->command->info("✅ Superadmin created: {$email}");
        $this->command->warn('⚠️  Remember to change the password after first login!');
    }

    /**
     * Create demo data for local/development
     */
    private function createDemoData(): void
    {
        $this->command->info('Creating demo data...');

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

        $admin->assignRole('admin');

        $this->call([
            LinkSeeder::class,
            QrCodeSeeder::class,
            PageSeeder::class,
            PageBlockSeeder::class,
        ]);

        $this->command->info('✅ Demo data created');
    }
}
