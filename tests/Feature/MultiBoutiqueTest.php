<?php

namespace Tests\Feature;

use App\Models\Boutique;
use App\Models\BoutiqueProduit;
use App\Models\Categorie;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiBoutiqueTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_gerant_sans_boutique_est_redirige_vers_la_creation_si_aucune_nexiste(): void
    {
        // La migration de backfill crée toujours une "Boutique principale" par
        // défaut ; on simule ici le cas limite où plus aucune boutique n'existe.
        Boutique::query()->delete();

        $gerant = User::factory()->create(['role' => 'gerant', 'active' => true, 'boutique_id' => null]);

        $this->actingAs($gerant)->get('/dashboard')->assertRedirect(route('boutiques.create'));
    }

    public function test_un_gerant_sans_boutique_est_redirige_vers_la_selection_si_des_boutiques_existent(): void
    {
        Boutique::create(['nom' => 'Boutique A', 'active' => true]);
        $gerant = User::factory()->create(['role' => 'gerant', 'active' => true, 'boutique_id' => null]);

        $this->actingAs($gerant)->get('/dashboard')->assertRedirect(route('boutiques.selectionner'));
    }

    public function test_un_gerant_peut_changer_de_boutique_active(): void
    {
        $boutiqueA = Boutique::create(['nom' => 'Boutique A', 'active' => true]);
        $boutiqueB = Boutique::create(['nom' => 'Boutique B', 'active' => true]);
        $gerant = User::factory()->create(['role' => 'gerant', 'active' => true, 'boutique_id' => null]);

        $this->actingAs($gerant)->post('/boutique/switch', ['boutique_id' => $boutiqueA->id])
            ->assertRedirect(route('dashboard'));

        $this->assertSame($boutiqueA->id, session('boutique_id'));

        $this->actingAs($gerant)->post('/boutique/switch', ['boutique_id' => $boutiqueB->id])
            ->assertRedirect(route('dashboard'));

        $this->assertSame($boutiqueB->id, session('boutique_id'));
    }

    public function test_un_caissier_ne_peut_pas_changer_de_boutique(): void
    {
        $boutique = Boutique::create(['nom' => 'Boutique A', 'active' => true]);
        $autreBoutique = Boutique::create(['nom' => 'Boutique B', 'active' => true]);
        $caissier = User::factory()->create(['role' => 'caissier', 'active' => true, 'boutique_id' => $boutique->id]);

        $this->actingAs($caissier)->post('/boutique/switch', ['boutique_id' => $autreBoutique->id])
            ->assertForbidden();
    }

    public function test_creer_une_boutique_initialise_le_stock_a_zero_pour_les_produits_existants(): void
    {
        $boutiqueExistante = Boutique::create(['nom' => 'Boutique existante', 'active' => true]);
        $gerant = User::factory()->create(['role' => 'gerant', 'active' => true, 'boutique_id' => $boutiqueExistante->id]);

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

        $this->actingAs($gerant)->post('/boutiques', [
            'nom' => 'Nouvelle boutique',
            'adresse' => 'Quelque part',
        ])->assertRedirect(route('boutiques.index'));

        $nouvelleBoutique = Boutique::where('nom', 'Nouvelle boutique')->firstOrFail();

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

        BoutiqueProduit::create(['boutique_id' => $boutiqueA->id, 'produit_id' => $produit->id, 'stock_actuel' => 15, 'stock_min' => 2, 'stock_max' => 100]);
        BoutiqueProduit::create(['boutique_id' => $boutiqueB->id, 'produit_id' => $produit->id, 'stock_actuel' => 3, 'stock_min' => 2, 'stock_max' => 100]);

        $caissierA = User::factory()->create(['role' => 'caissier', 'active' => true, 'boutique_id' => $boutiqueA->id]);
        $caissierB = User::factory()->create(['role' => 'caissier', 'active' => true, 'boutique_id' => $boutiqueB->id]);

        $this->actingAs($caissierA)->get('/stocks')->assertOk()->assertSee('15');
        $this->actingAs($caissierB)->get('/stocks')->assertOk()->assertSee('3');
    }
}
