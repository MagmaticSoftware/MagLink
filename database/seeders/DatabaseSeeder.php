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
        // Crea il tenant se non esiste
        $tenant = Tenant::firstOrCreate(['id' => 'demo'], ['name' => 'Demo Tenant']);

        // Crea il dominio se non esiste
        $tenant->domains()->firstOrCreate(['domain' => 'maglink.localhost']);

        $tenant->run(function () {
            User::factory()->create([
                    'name' => 'Demo User',
                    'email' => 'demo@demo.com',
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
