<?php

namespace App\Support;

use App\Models\Produit;

class ValidePrixCatalogue
{
    /**
     * Vérifie que chaque ligne correspond au prix de vente du catalogue.
     *
     * @param array<int, array{produit_id: int, prix_unitaire: float|int|string}> $lignes
     */
    public static function prixConformes(array $lignes): bool
    {
        if ($lignes === []) {
            return true;
        }

        $ids = array_column($lignes, 'produit_id');
        $prixCatalogue = Produit::whereIn('id', $ids)->pluck('prix_vente', 'id');

        foreach ($lignes as $ligne) {
            $prixAttendu = (float) ($prixCatalogue[$ligne['produit_id']] ?? 0);
            $prixSoumis = (float) $ligne['prix_unitaire'];
            if (abs($prixAttendu - $prixSoumis) > 0.01) {
                return false;
            }
        }

        return true;
    }
}
