<?php

namespace Database\Seeders;

use App\Models\Entreprise;
use Illuminate\Database\Seeder;

class EntrepriseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Entreprise::updateOrCreate(
            ['id' => 1],
            [
                'nom' => 'GesBoutique SARL',
                'nif' => 'NIF123456789',
                'adresse' => '123 Rue du Commerce, Quartier Affaires, 75001 Paris, France',
                'telephone' => '+221 77 123 45 67',
                'email' => 'contact@gesboutique.com',
                'logo' => 'logo-gesboutique.png',
                'site_web' => 'www.gesboutique.com',
            ]
        );
    }
}
