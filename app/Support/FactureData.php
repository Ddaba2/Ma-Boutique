<?php

namespace App\Support;

use App\Models\Entreprise;
use App\Models\Vente;

class FactureData
{
    public static function for(Vente $vente, ?Entreprise $entreprise = null): array
    {
        $defaults = config('facture.entreprise', []);

        return [
            'logo' => self::resolveLogoPath($entreprise),
            'entreprise' => [
                'nom'       => $entreprise?->nom ?: ($defaults['nom'] ?? 'GesBoutique'),
                'slogan'    => $defaults['slogan'] ?? '',
                'nif'       => $entreprise?->nif ?: ($defaults['nif'] ?? ''),
                'adresse'   => $entreprise?->adresse ?: ($defaults['adresse'] ?? ''),
                'telephone' => $entreprise?->telephone ?: ($defaults['telephone'] ?? ''),
                'email'     => $entreprise?->email ?: ($defaults['email'] ?? ''),
                'site_web'  => $entreprise?->site_web ?: ($defaults['site_web'] ?? ''),
            ],
            'client' => [
                'nom'       => $vente->client_nom
                    ?: ($vente->relationLoaded('client') ? $vente->client?->nom_complet : null)
                    ?: 'Client comptant',
                'telephone' => $vente->client_telephone
                    ?: ($vente->relationLoaded('client') ? $vente->client?->telephone : null),
                'email'     => $vente->client_email
                    ?: ($vente->relationLoaded('client') ? $vente->client?->email : null),
                'adresse'   => ($vente->relationLoaded('client') ? $vente->client?->adresse : null)
                    ?: config('facture.client_adresse_defaut'),
            ],
            'conditions_paiement' => config('facture.conditions_paiement'),
            'mention_legale'      => config('facture.mention_legale'),
        ];
    }

    private static function resolveLogoPath(?Entreprise $entreprise): ?string
    {
        if ($entreprise?->logo) {
            $storagePath = storage_path('app/public/' . ltrim($entreprise->logo, '/'));
            if (is_file($storagePath)) {
                return $storagePath;
            }

            $publicStoragePath = public_path('storage/' . ltrim($entreprise->logo, '/'));
            if (is_file($publicStoragePath)) {
                return $publicStoragePath;
            }
        }

        $configLogo = config('facture.logo_path');
        if ($configLogo) {
            $publicPath = public_path(ltrim($configLogo, '/'));
            if (is_file($publicPath)) {
                return $publicPath;
            }
        }

        return null;
    }
}
