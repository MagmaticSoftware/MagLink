<?php

namespace Database\Seeders;

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
        $tenant1 = Tenant::firstOrCreate(['id' => 'demo'], ['name' => 'Demo Tenant']);
        $tenant1->run(function () {
            User::factory()->create([
                    'name' => 'Demo User',
                    'email' => 'demo@demo.com',
                ]);
        });

        $tenant2 = Tenant::firstOrCreate(['id' => 'maglink'], ['name' => 'Maglink']);
        $tenant2->run(function () {
            User::factory()->create([
                    'name' => 'Maglink User',
                    'email' => 'mag@demo.com',
                ]);
        });

        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@demo.com',
        ]);

        Role::findOrCreate('admin', 'web');

        $admin->assignRole('admin');
    }
}
