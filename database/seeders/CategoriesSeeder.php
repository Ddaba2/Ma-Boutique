<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categorie;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'nom' => 'Boissons',
                'description' => 'Toutes sortes de boissons',
                'couleur' => '#3B82F6',
                'active' => true
            ],
            [
                'nom' => 'Alimentation',
                'description' => 'Produits alimentaires de base',
                'couleur' => '#10B981',
                'active' => true
            ],
            [
                'nom' => 'Électronique',
                'description' => 'Appareils électroniques et accessoires',
                'couleur' => '#8B5CF6',
                'active' => true
            ],
            [
                'nom' => 'Vêtements',
                'description' => 'Habits et accessoires',
                'couleur' => '#F59E0B',
                'active' => true
            ],
            [
                'nom' => 'Maison',
                'description' => 'Articles pour la maison',
                'couleur' => '#EF4444',
                'active' => true
            ],
            [
                'nom' => 'Hygiène',
                'description' => 'Produits de beauté et hygiène',
                'couleur' => '#EC4899',
                'active' => true
            ]
        ];

        foreach ($categories as $categorie) {
            Categorie::firstOrCreate(
                ['nom' => $categorie['nom']],
                $categorie
            );
        }
    }
}
