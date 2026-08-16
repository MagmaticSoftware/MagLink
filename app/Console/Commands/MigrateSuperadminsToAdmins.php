<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class MigrateSuperadminsToAdmins extends Command
{
    protected $signature = 'admin:migrate-superadmins';

    protected $description = 'Migra gli utenti con ruolo superadmin (guard web) verso la nuova tabella admins (guard admin)';

    public function handle(): int
    {
        Role::findOrCreate('superadmin', 'admin');

        $superadmins = User::role('superadmin', 'web')->get();

        if ($superadmins->isEmpty()) {
            $this->info('Nessun utente con ruolo superadmin da migrare.');
            return self::SUCCESS;
        }

        foreach ($superadmins as $user) {
            $admin = Admin::withTrashed()->where('email', $user->email)->first();

            if (!$admin) {
                $admin = Admin::create([
                    'name' => trim("{$user->first_name} {$user->last_name}") ?: $user->email,
                    'email' => $user->email,
                    'password' => $user->password,
                    'email_verified_at' => $user->email_verified_at,
                ]);
                $this->info("Creato admin: {$admin->email}");
            } else {
                $this->warn("Admin già esistente, salto la creazione: {$admin->email}");
            }

            if (!$admin->hasRole('superadmin')) {
                $admin->assignRole('superadmin');
            }

            $user->removeRole('superadmin');
            $this->info("Ruolo superadmin rimosso dall'utente: {$user->email}");
        }

        $this->info('Migrazione completata.');

        return self::SUCCESS;
    }
}
