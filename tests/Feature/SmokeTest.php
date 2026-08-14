<?php

namespace Tests\Feature;

use App\Models\Boutique;
use App\Models\BoutiqueProduit;
use App\Models\Categorie;
use App\Models\ClotureCaisse;
use App\Models\Produit;
use App\Models\User;
use App\Services\VenteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Visite toutes les pages GET de l'application avec un compte gérant (le rôle
 * qui voit le plus de pages) pour détecter les vues manquantes, routes cassées
 * ou erreurs 500 qui ne sont couvertes par aucun autre test.
 */
class SmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_toutes_les_pages_repondent_sans_erreur_serveur(): void
    {
        $boutique = Boutique::create(['nom' => 'Boutique test', 'active' => true]);
        $gerant = User::factory()->create(['role' => 'gerant', 'active' => true, 'boutique_id' => $boutique->id]);

        $categorie = Categorie::create(['nom' => 'Catégorie test', 'active' => true]);
        $produit = Produit::create([
            'reference' => Produit::generateReference(),
            'nom' => 'Produit test',
            'prix_achat' => 500,
            'prix_vente' => 1000,
            'stock_actuel' => 10,
            'stock_min' => 2,
            'stock_max' => 100,
            'categorie_id' => $categorie->id,
            'active' => true,
        ]);
        BoutiqueProduit::create([
            'boutique_id' => $boutique->id,
            'produit_id' => $produit->id,
            'stock_actuel' => 10,
            'stock_min' => 2,
            'stock_max' => 100,
        ]);

        session(['boutique_id' => $boutique->id]);
        $vente = app(VenteService::class)->creerVente([
            'client_nom' => 'Client Test',
            'client_telephone' => null,
            'mode_paiement' => 'espece',
            'montant_recu' => 1000,
            'lignes' => [
                ['produit_id' => $produit->id, 'quantite' => 1, 'prix_unitaire' => 1000],
            ],
        ], $boutique->id);

        $cloture = ClotureCaisse::create([
            'date' => now()->toDateString(),
            'boutique_id' => $boutique->id,
            'fond_ouverture' => 5000,
            'montant_compte' => 5000,
            'total_especes' => 0,
            'ecart' => 0,
            'statut' => 'clos',
            'user_id' => $gerant->id,
        ]);

        $urls = [
            '/dashboard',
            '/caisse', '/caisse/create', '/caisse/'.$cloture->id,
            '/categories', '/categories/'.$categorie->id, '/categories/'.$categorie->id.'/edit',
            '/produits', '/produits-barcodes', '/produits-import', '/produits/create',
            '/produits/'.$produit->id, '/produits/'.$produit->id.'/barcode', '/produits/'.$produit->id.'/edit',
            '/rapports', '/rapports/export/pdf/complete', '/rapports/export/pdf/stocks', '/rapports/export/pdf/ventes', '/rapports/export/ventes',
            '/rapports/performances', '/rapports/stocks', '/rapports/ventes',
            '/stocks', '/stocks/create', '/stocks/mouvements',
            '/stocks/'.$produit->id.'/historique', '/stocks/'.$produit->id.'/ajuster',
            '/ventes', '/ventes/create',
            '/parametres', '/utilisateurs/create', '/utilisateurs/'.$gerant->id.'/edit',
            '/ventes/'.$vente->id, '/ventes/'.$vente->id.'/facture', '/ventes/'.$vente->id.'/ticket',
            '/ventes/'.$vente->id.'/edit',
        ];

        $echecs = [];
        foreach ($urls as $url) {
            $status = $this->actingAs($gerant)->get($url)->getStatusCode();
            if ($status >= 500) {
                $echecs[] = "$url -> HTTP $status";
            }
        }

        $this->assertEmpty($echecs, "Pages en erreur serveur :\n".implode("\n", $echecs));
    }
}
