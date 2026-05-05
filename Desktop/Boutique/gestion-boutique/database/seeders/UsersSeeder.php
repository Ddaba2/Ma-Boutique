<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // Créer l'utilisateur administrateur
        User::create([
            'name' => 'Administrateur',
            'email' => 'admin@gesbou.com',
            'password' => Hash::make('admin123'),
        ]);

        // Créer un utilisateur de test
        User::create([
            'name' => 'Utilisateur Test',
            'email' => 'test@gesbou.com',
            'password' => Hash::make('test123'),
        ]);
    }
}
