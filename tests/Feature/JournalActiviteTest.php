<?php

namespace Tests\Feature;

use App\Models\Boutique;
use App\Models\BoutiqueProduit;
use App\Models\Categorie;
use App\Models\JournalActivite;
use App\Models\Produit;
use App\Models\User;
use App\Services\VenteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalActiviteTest extends TestCase
{
    use RefreshDatabase;

    private function boutique(): Boutique
    {
        return Boutique::create(['nom' => 'Boutique test', 'active' => true]);
    }

    private function produit(Boutique $boutique, int $stock = 20): Produit
    {
        $categorie = Categorie::create(['nom' => 'Catégorie test', 'active' => true]);
        $produit = Produit::create([
            'reference' => Produit::generateReference(),
            'nom' => 'Produit test',
            'prix_achat' => 500,
            'prix_vente' => 1000,
            'stock_actuel' => $stock,
            'stock_min' => 2,
            'stock_max' => 100,
            'categorie_id' => $categorie->id,
            'active' => true,
        ]);

        BoutiqueProduit::create([
            'boutique_id' => $boutique->id,
            'produit_id' => $produit->id,
            'stock_actuel' => $stock,
            'stock_min' => 2,
            'stock_max' => 100,
        ]);

        return $produit;
    }

    public function test_modifier_le_prix_dun_produit_est_journalise(): void
    {
        $boutique = $this->boutique();
        $gestionnaire = User::factory()->create(['role' => 'gestionnaire', 'active' => true, 'boutique_id' => $boutique->id]);
        $produit = $this->produit($boutique);

        $this->actingAs($gestionnaire)->put(route('produits.update', $produit), [
            'nom' => $produit->nom,
            'prix_achat' => 600,
            'prix_vente' => 1200,
            'categorie_id' => $produit->categorie_id,
        ]);

        $entree = JournalActivite::where('action', 'produit.prix_modifie')->first();
        $this->assertNotNull($entree);
        $this->assertSame($gestionnaire->id, $entree->user_id);
        $this->assertStringContainsString('500 → 600', $entree->description);
        $this->assertStringContainsString('1000 → 1200', $entree->description);
    }

    public function test_modifier_un_produit_sans_changer_le_prix_ne_journalise_rien(): void
    {
        $boutique = $this->boutique();
        $gestionnaire = User::factory()->create(['role' => 'gestionnaire', 'active' => true, 'boutique_id' => $boutique->id]);
        $produit = $this->produit($boutique);

        $this->actingAs($gestionnaire)->put(route('produits.update', $produit), [
            'nom' => 'Nouveau nom',
            'prix_achat' => $produit->prix_achat,
            'prix_vente' => $produit->prix_vente,
            'categorie_id' => $produit->categorie_id,
        ]);

        $this->assertSame(0, JournalActivite::where('action', 'produit.prix_modifie')->count());
    }

    public function test_annuler_une_vente_est_journalise(): void
    {
        $boutique = $this->boutique();
        $gerant = User::factory()->create(['role' => 'gerant', 'active' => true, 'boutique_id' => $boutique->id]);
        $produit = $this->produit($boutique);

        $vente = app(VenteService::class)->creerVente([
            'client_nom' => 'Client comptant',
            'client_telephone' => null,
            'mode_paiement' => 'espece',
            'montant_recu' => 1000,
            'lignes' => [
                ['produit_id' => $produit->id, 'quantite' => 1, 'prix_unitaire' => 1000],
            ],
        ], $boutique->id);

        $this->actingAs($gerant)->delete(route('ventes.destroy', $vente));

        $entree = JournalActivite::where('action', 'vente.annulee')->first();
        $this->assertNotNull($entree);
        $this->assertSame($gerant->id, $entree->user_id);
        $this->assertStringContainsString($vente->reference, $entree->description);
    }

    public function test_ajuster_le_stock_est_journalise(): void
    {
        $boutique = $this->boutique();
        $gestionnaire = User::factory()->create(['role' => 'gestionnaire', 'active' => true, 'boutique_id' => $boutique->id]);
        $produit = $this->produit($boutique, stock: 20);

        $this->actingAs($gestionnaire)->post(route('stocks.ajuster', $produit), [
            'nouveau_stock' => 15,
            'motif' => 'Inventaire physique',
        ]);

        $entree = JournalActivite::where('action', 'stock.ajuste')->first();
        $this->assertNotNull($entree);
        $this->assertSame($gestionnaire->id, $entree->user_id);
        $this->assertStringContainsString('20 → 15', $entree->description);
    }

    public function test_le_journal_saffiche_dans_longlet_parametres(): void
    {
        $boutique = $this->boutique();
        $gerant = User::factory()->create(['role' => 'gerant', 'active' => true, 'boutique_id' => $boutique->id]);

        // boutique_id explicite : enregistrer() le déduit de la session, qui
        // n'est pas encore initialisée hors d'une requête HTTP.
        JournalActivite::create([
            'boutique_id' => $boutique->id,
            'action' => 'stock.ajuste',
            'description' => 'Entrée de test dans le journal',
        ]);

        $this->actingAs($gerant)->get(route('parametres.index', ['onglet' => 'journal']))
            ->assertOk()
            ->assertSee('Entrée de test dans le journal');
    }
}
