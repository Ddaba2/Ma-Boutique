<?php

namespace Tests\Feature;

use App\Models\Boutique;
use App\Models\BoutiqueProduit;
use App\Models\Categorie;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * L'application reste techniquement multi-boutique (boutique_id, scope
 * automatique, stock séparé) mais n'expose plus d'écran de gestion — il n'y a
 * jamais qu'une seule boutique active en pratique, sélectionnée
 * automatiquement (voir EnsureBoutiqueSelected). Ces tests couvrent ce qui
 * reste réellement en jeu : l'isolation du stock et l'amorçage automatique
 * d'une boutique nouvellement créée (par tinker/seeder).
 */
class MultiBoutiqueTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_gerant_sans_boutique_assignee_bascule_automatiquement_sur_lunique_boutique_active(): void
    {
        $boutique = Boutique::create(['nom' => 'Boutique unique', 'active' => true]);
        $gerant = User::factory()->create(['role' => 'gerant', 'active' => true, 'boutique_id' => null]);

        $this->actingAs($gerant)->get('/dashboard')->assertOk();
        $this->assertSame($boutique->id, session('boutique_id'));
    }

    public function test_creer_une_boutique_initialise_le_stock_a_zero_pour_les_produits_existants(): void
    {
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

        $nouvelleBoutique = Boutique::create(['nom' => 'Nouvelle boutique', 'active' => true]);

        $stock = BoutiqueProduit::withoutGlobalScopes()
            ->where('boutique_id', $nouvelleBoutique->id)
            ->where('produit_id', $produit->id)
            ->first();

        $this->assertNotNull($stock);
        $this->assertSame(0, $stock->stock_actuel);
    }

    public function test_le_stock_affiche_est_isole_par_boutique(): void
    {
        $boutiqueA = Boutique::create(['nom' => 'Boutique A', 'active' => true]);
        $boutiqueB = Boutique::create(['nom' => 'Boutique B', 'active' => true]);

        $categorie = Categorie::create(['nom' => 'Catégorie test', 'active' => true]);
        $produit = Produit::create([
            'reference' => Produit::generateReference(),
            'nom' => 'Produit isolé',
            'prix_achat' => 500,
            'prix_vente' => 1000,
            'stock_actuel' => 10,
            'stock_min' => 2,
            'stock_max' => 100,
            'categorie_id' => $categorie->id,
            'active' => true,
        ]);

        // Le produit est créé après les boutiques : l'amorçage automatique de
        // Boutique::booted() (pour les produits déjà existants) ne s'applique
        // pas ici, on crée donc les lignes de stock explicitement.
        BoutiqueProduit::create(['boutique_id' => $boutiqueA->id, 'produit_id' => $produit->id, 'stock_actuel' => 15, 'stock_min' => 2, 'stock_max' => 100]);
        BoutiqueProduit::create(['boutique_id' => $boutiqueB->id, 'produit_id' => $produit->id, 'stock_actuel' => 3, 'stock_min' => 2, 'stock_max' => 100]);

        $caissierA = User::factory()->create(['role' => 'caissier', 'active' => true, 'boutique_id' => $boutiqueA->id]);
        $caissierB = User::factory()->create(['role' => 'caissier', 'active' => true, 'boutique_id' => $boutiqueB->id]);

        $this->actingAs($caissierA)->get('/stocks')->assertOk()->assertSee('15');
        $this->actingAs($caissierB)->get('/stocks')->assertOk()->assertSee('3');
    }
}
