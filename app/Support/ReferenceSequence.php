<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Génère des références métier lisibles (VENTE2026..., PROD..., CMD2026...)
 * à partir d'un compteur dédié par clé, incrémenté sous verrou. Contrairement
 * à un count() des lignes existantes, ce compteur ne recule jamais quand une
 * ligne est supprimée, donc il ne peut pas régénérer une référence déjà prise.
 */
class ReferenceSequence
{
    public static function next(string $cle, string $prefixe, int $largeur = 5): string
    {
        return DB::transaction(function () use ($cle, $prefixe, $largeur) {
            $ligne = DB::table('reference_sequences')->where('cle', $cle)->lockForUpdate()->first();

            if ($ligne) {
                $valeur = $ligne->valeur + 1;
                DB::table('reference_sequences')->where('cle', $cle)->update([
                    'valeur' => $valeur,
                    'updated_at' => now(),
                ]);
            } else {
                $valeur = 1;
                DB::table('reference_sequences')->insert([
                    'cle' => $cle,
                    'valeur' => $valeur,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return $prefixe . str_pad((string) $valeur, $largeur, '0', STR_PAD_LEFT);
        });
    }
}
