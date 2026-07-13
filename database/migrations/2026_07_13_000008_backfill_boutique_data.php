<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $boutiqueId = DB::table('boutiques')->insertGetId([
            'nom' => 'Boutique principale',
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $produits = DB::table('produits')->select('id', 'stock_actuel', 'stock_min', 'stock_max')->get();
        foreach ($produits as $produit) {
            DB::table('boutique_produit')->insert([
                'boutique_id' => $boutiqueId,
                'produit_id' => $produit->id,
                'stock_actuel' => $produit->stock_actuel,
                'stock_min' => $produit->stock_min,
                'stock_max' => $produit->stock_max,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Les gérants restent sans boutique fixe (ils supervisent toutes les boutiques).
        DB::table('users')->where('role', '!=', 'gerant')->update(['boutique_id' => $boutiqueId]);

        DB::table('ventes')->update(['boutique_id' => $boutiqueId]);
        DB::table('mouvements_stock')->update(['boutique_id' => $boutiqueId]);
        DB::table('clotures_caisse')->update(['boutique_id' => $boutiqueId]);
        DB::table('commandes')->update(['boutique_id' => $boutiqueId]);
    }

    public function down(): void
    {
        DB::table('commandes')->update(['boutique_id' => null]);
        DB::table('clotures_caisse')->update(['boutique_id' => null]);
        DB::table('mouvements_stock')->update(['boutique_id' => null]);
        DB::table('ventes')->update(['boutique_id' => null]);
        DB::table('users')->update(['boutique_id' => null]);
        DB::table('boutique_produit')->truncate();
        DB::table('boutiques')->truncate();
    }
};
