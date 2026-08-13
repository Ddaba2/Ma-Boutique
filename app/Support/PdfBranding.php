<?php

namespace App\Support;

use App\Models\Entreprise;

class PdfBranding
{
    public static function resolve(): array
    {
        $entreprise = Entreprise::first();
        $defaults = config('facture.entreprise', []);

        return [
            'nom'       => $entreprise?->nom ?? ($defaults['nom'] ?? 'GesBoutique'),
            'nif'       => $entreprise?->nif ?? ($defaults['nif'] ?? ''),
            'adresse'   => $entreprise?->adresse ?? ($defaults['adresse'] ?? ''),
            'telephone' => $entreprise?->telephone ?? ($defaults['telephone'] ?? ''),
            'email'     => $entreprise?->email ?? ($defaults['email'] ?? ''),
            'site_web'  => $entreprise?->site_web ?? ($defaults['site_web'] ?? ''),
            'logoPath'  => self::logoPath($entreprise),
        ];
    }

    public static function logoPath(?Entreprise $entreprise = null): ?string
    {
        $entreprise ??= Entreprise::first();

        if ($entreprise?->logo) {
            $storagePath = storage_path('app/public/' . $entreprise->logo);
            if (is_file($storagePath)) {
                return $storagePath;
            }

            $publicPath = public_path($entreprise->logo);
            if (is_file($publicPath)) {
                return $publicPath;
            }
        }

        $configured = config('facture.logo_path');
        if ($configured) {
            $configuredPath = public_path($configured);
            if (is_file($configuredPath) && filesize($configuredPath) > 200) {
                return $configuredPath;
            }
        }

        foreach (['logo-client.svg', 'logo-client.png', 'logo-gesboutique.png'] as $fallback) {
            $path = public_path($fallback);
            if (is_file($path) && filesize($path) > 200) {
                return $path;
            }
        }

        return null;
    }

    /** URL publique du logo pour les vues Blade (sidebar, aperçu facture). */
    public static function logoUrl(?Entreprise $entreprise = null): ?string
    {
        $path = self::logoPath($entreprise);
        if (!$path) {
            return null;
        }

        $publicRoot = realpath(public_path()) ?: public_path();
        $real = realpath($path) ?: $path;

        if (str_starts_with(str_replace('\\', '/', $real), str_replace('\\', '/', $publicRoot))) {
            $relative = ltrim(str_replace('\\', '/', substr($real, strlen($publicRoot))), '/');

            return asset($relative);
        }

        if ($entreprise?->logo && is_file(storage_path('app/public/' . $entreprise->logo))) {
            return asset('storage/' . $entreprise->logo);
        }

        return null;
    }

    /** @return array{entreprise: ?Entreprise, logoPath: ?string, branding: array} */
    public static function forView(): array
    {
        $entreprise = Entreprise::first();
        $branding = self::resolve();

        return [
            'entreprise' => $entreprise,
            'logoPath'   => $branding['logoPath'],
            'branding'   => $branding,
        ];
    }
}
