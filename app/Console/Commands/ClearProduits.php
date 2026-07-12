<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Produit;
use Illuminate\Support\Facades\DB;

class ClearProduits extends Command
{
    protected $signature = 'produits:clear';
    protected $description = 'Vider tous les produits de la base de données';

    public function handle()
    {
        $count = Produit::count();

        if ($count === 0) {
            $this->info('Aucun produit à supprimer.');
            return 0;
        }

        if ($this->confirm("Êtes-vous sûr de vouloir supprimer les $count produits ?")) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            Produit::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->info("✓ $count produit(s) supprimé(s) avec succès.");
            return 0;
        }

        $this->info('Opération annulée.');
        return 0;
    }
}
