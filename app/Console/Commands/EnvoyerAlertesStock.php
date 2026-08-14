<?php

namespace App\Console\Commands;

use App\Mail\AlerteStockMail;
use App\Models\Boutique;
use App\Models\BoutiqueProduit;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnvoyerAlertesStock extends Command
{
    protected $signature = 'alertes:stock';

    protected $description = 'Envoie une alerte email aux gérants pour chaque boutique ayant des produits en rupture ou en stock faible';

    public function handle(): int
    {
        $gerants = User::where('role', 'gerant')->where('active', true)->get();

        if ($gerants->isEmpty()) {
            $this->info('Aucun gérant actif à notifier.');

            return 0;
        }

        $envois = 0;

        foreach (Boutique::where('active', true)->get() as $boutique) {
            $enRupture = BoutiqueProduit::dansBoutique($boutique->id)->with('produit')->enRupture()->get();
            $stockFaible = BoutiqueProduit::dansBoutique($boutique->id)->with('produit')->stockFaible()->get();

            if ($enRupture->isEmpty() && $stockFaible->isEmpty()) {
                continue;
            }

            foreach ($gerants as $gerant) {
                Mail::to($gerant->email)->queue(new AlerteStockMail($boutique, $enRupture, $stockFaible));
            }

            $envois++;
        }

        $this->info("Alertes envoyées pour $envois boutique(s).");

        return 0;
    }
}
