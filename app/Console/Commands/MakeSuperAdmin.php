<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class MakeSuperAdmin extends Command
{
    protected $signature = 'admin:make-superadmin
                            {--email= : Email dell\'utente esistente o nuovo}
                            {--name= : Nome (solo se creazione nuovo utente)}
                            {--password= : Password (solo se creazione nuovo utente)}';

    protected $description = 'Assegna o crea un utente con il ruolo superadmin per accedere al pannello Filament';

    public function handle(): int
    {
        $email = $this->option('email') ?? $this->ask('Email');

        $user = User::withTrashed()->where('email', $email)->first();

        if ($user) {
            $this->info("Utente trovato: {$user->first_name} {$user->last_name} <{$user->email}>");
        } else {
            $this->info('Utente non trovato. Creazione nuovo account superadmin...');

            $firstName = $this->option('name') ?? $this->ask('Nome');
            $password = $this->option('password') ?? $this->secret('Password');

            $user = User::create([
                'first_name' => $firstName,
                'last_name'  => 'Admin',
                'email'      => $email,
                'password'   => Hash::make($password),
                'email_verified_at' => now(),
            ]);

            $this->info("Utente creato: {$user->email}");
        }

        // Assicura che il ruolo esista
        Role::findOrCreate('superadmin', 'web');

        $user->assignRole('superadmin');

        $this->info("Ruolo superadmin assegnato a {$user->email}.");
        $this->line('');
        $this->line('Accedi al pannello admin su: <fg=cyan>/admin</>');

        return self::SUCCESS;
    }
}
