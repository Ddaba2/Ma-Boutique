<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produit;
use App\Models\Categorie;
use App\Models\Boutique;
use App\Models\BoutiqueProduit;

class ProduitsSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Categorie::all();
        $boutiquePrincipale = Boutique::first();

        $produits = [
            // Boissons
            ['nom' => 'Coca-Cola 33cl', 'prix_achat' => 150, 'prix_vente' => 250, 'stock_actuel' => 50, 'stock_min' => 10, 'categorie' => 'Boissons'],
            ['nom' => 'Eau Minérale 1.5L', 'prix_achat' => 100, 'prix_vente' => 200, 'stock_actuel' => 30, 'stock_min' => 15, 'categorie' => 'Boissons'],
            ['nom' => 'Jus d\'Orange 1L', 'prix_achat' => 300, 'prix_vente' => 500, 'stock_actuel' => 20, 'stock_min' => 8, 'categorie' => 'Boissons'],

            // Alimentation
            ['nom' => 'Pain de mie', 'prix_achat' => 800, 'prix_vente' => 1200, 'stock_actuel' => 15, 'stock_min' => 5, 'categorie' => 'Alimentation'],
            ['nom' => 'Riz 1kg', 'prix_achat' => 1500, 'prix_vente' => 2500, 'stock_actuel' => 25, 'stock_min' => 10, 'categorie' => 'Alimentation'],
            ['nom' => 'Huile végétale 1L', 'prix_achat' => 1200, 'prix_vente' => 2000, 'stock_actuel' => 18, 'stock_min' => 6, 'categorie' => 'Alimentation'],

            // Électronique
            ['nom' => 'Chargeur USB', 'prix_achat' => 1500, 'prix_vente' => 2500, 'stock_actuel' => 12, 'stock_min' => 5, 'categorie' => 'Électronique'],
            ['nom' => 'Écouteurs Bluetooth', 'prix_achat' => 5000, 'prix_vente' => 8000, 'stock_actuel' => 8, 'stock_min' => 3, 'categorie' => 'Électronique'],
            ['nom' => 'Power Bank 10000mAh', 'prix_achat' => 4000, 'prix_vente' => 7000, 'stock_actuel' => 6, 'stock_min' => 2, 'categorie' => 'Électronique'],

            // Vêtements
            ['nom' => 'T-shirt Homme', 'prix_achat' => 2500, 'prix_vente' => 4000, 'stock_actuel' => 20, 'stock_min' => 8, 'categorie' => 'Vêtements'],
            ['nom' => 'Jean Femme', 'prix_achat' => 8000, 'prix_vente' => 12000, 'stock_actuel' => 10, 'stock_min' => 4, 'categorie' => 'Vêtements'],
            ['nom' => 'Chaussures Sport', 'prix_achat' => 10000, 'prix_vente' => 15000, 'stock_actuel' => 8, 'stock_min' => 3, 'categorie' => 'Vêtements'],

            // Maison
            ['nom' => 'Savon liquide 1L', 'prix_achat' => 800, 'prix_vente' => 1500, 'stock_actuel' => 25, 'stock_min' => 10, 'categorie' => 'Maison'],
            ['nom' => 'Sacs poubelle', 'prix_achat' => 1500, 'prix_vente' => 2500, 'stock_actuel' => 30, 'stock_min' => 12, 'categorie' => 'Maison'],
            ['nom' => 'Ampoule LED', 'prix_achat' => 1000, 'prix_vente' => 1800, 'stock_actuel' => 40, 'stock_min' => 15, 'categorie' => 'Maison'],

            // Hygiène
            ['nom' => 'Shampoing 300ml', 'prix_achat' => 2000, 'prix_vente' => 3500, 'stock_actuel' => 15, 'stock_min' => 6, 'categorie' => 'Hygiène'],
            ['nom' => 'Dentifrice', 'prix_achat' => 800, 'prix_vente' => 1500, 'stock_actuel' => 20, 'stock_min' => 8, 'categorie' => 'Hygiène'],
            ['nom' => 'Déodorant', 'prix_achat' => 1500, 'prix_vente' => 2800, 'stock_actuel' => 12, 'stock_min' => 5, 'categorie' => 'Hygiène']
        ];

        foreach ($produits as $index => $produitData) {
            $categorie = $categories->where('nom', $produitData['categorie'])->first();
            $stockMax = $produitData['stock_actuel'] * 3;

            $produit = Produit::firstOrCreate(
                ['reference' => 'PROD' . str_pad($index + 1, 6, '0', STR_PAD_LEFT)],
                [
                    'nom' => $produitData['nom'],
                    'description' => 'Produit de qualité supérieure',
                    'prix_achat' => $produitData['prix_achat'],
                    'prix_vente' => $produitData['prix_vente'],
                    'stock_actuel' => $produitData['stock_actuel'],
                    'stock_min' => $produitData['stock_min'],
                    'stock_max' => $stockMax,
                    'categorie_id' => $categorie->id,
                    'active' => true
                ]
            );

            if ($boutiquePrincipale && !BoutiqueProduit::where('boutique_id', $boutiquePrincipale->id)->where('produit_id', $produit->id)->exists()) {
                BoutiqueProduit::create([
                    'boutique_id' => $boutiquePrincipale->id,
                    'produit_id' => $produit->id,
                    'stock_actuel' => $produitData['stock_actuel'],
                    'stock_min' => $produitData['stock_min'],
                    'stock_max' => $stockMax,
                ]);
            }
        }
    }
}
