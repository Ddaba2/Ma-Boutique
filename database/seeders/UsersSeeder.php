<?php

namespace Database\Seeders;

use App\Models\Boutique;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $boutiquePrincipale = Boutique::first();

        if (! User::where('email', 'admin@gesbou.com')->exists()) {
            $password = Str::password(12);

            User::create([
                'name' => 'Administrateur',
                'email' => 'admin@gesbou.com',
                'password' => Hash::make($password),
                'role' => 'gerant',
                'active' => true,
            ]);

            $this->command?->warn("Compte gérant créé : admin@gesbou.com / {$password}");
            $this->command?->warn('Notez ce mot de passe et changez-le dès la première connexion.');
        } else {
            DB::table('users')->where('email', 'admin@gesbou.com')->update(['role' => 'gerant', 'active' => true]);
        }

        // Compte de démonstration : uniquement en local/tests, jamais seedé en production.
        if (app()->environment(['local', 'testing']) && ! User::where('email', 'test@gesbou.com')->exists()) {
            User::create([
                'name' => 'Utilisateur Test',
                'email' => 'test@gesbou.com',
                'password' => Hash::make('test123'),
                'role' => 'caissier',
                'boutique_id' => $boutiquePrincipale?->id,
                'active' => true,
            ]);
        }
    }
}
