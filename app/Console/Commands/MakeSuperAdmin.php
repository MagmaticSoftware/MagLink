<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class MakeSuperAdmin extends Command
{
    protected $signature = 'admin:make-superadmin
                            {--email= : Email dell\'admin esistente o nuovo}
                            {--name= : Nome (solo se creazione nuovo admin)}
                            {--password= : Password (solo se creazione nuovo admin)}';

    protected $description = 'Assegna o crea un account Admin con il ruolo superadmin per accedere al pannello Filament';

    public function handle(): int
    {
        $email = $this->option('email') ?? $this->ask('Email');

        $admin = Admin::withTrashed()->where('email', $email)->first();

        if ($admin) {
            $this->info("Admin trovato: {$admin->name} <{$admin->email}>");
        } else {
            $this->info('Admin non trovato. Creazione nuovo account superadmin...');

            $name = $this->option('name') ?? $this->ask('Nome');
            $password = $this->option('password') ?? $this->secret('Password');

            $admin = Admin::create([
                'name'     => $name,
                'email'    => $email,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]);

            $this->info("Admin creato: {$admin->email}");
        }

        // Assicura che il ruolo esista sul guard admin
        Role::findOrCreate('superadmin', 'admin');

        $admin->assignRole('superadmin');

        $this->info("Ruolo superadmin assegnato a {$admin->email}.");
        $this->line('');
        $this->line('Accedi al pannello admin su: <fg=cyan>/admin</>');

        return self::SUCCESS;
    }
}
