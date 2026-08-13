<?php

namespace Tests\Feature;

use App\Models\Boutique;
use App\Models\Categorie;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private function boutique(): Boutique
    {
        return Boutique::create(['nom' => 'Boutique test', 'active' => true]);
    }

    public function test_un_visiteur_non_connecte_est_redirige_vers_le_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_un_caissier_ne_peut_pas_acceder_a_limport_produits(): void
    {
        $caissier = User::factory()->create(['role' => 'caissier', 'active' => true, 'boutique_id' => $this->boutique()->id]);

        $this->actingAs($caissier)->get('/produits-import')->assertForbidden();
    }

    public function test_un_gestionnaire_peut_acceder_a_limport_produits(): void
    {
        $gestionnaire = User::factory()->create(['role' => 'gestionnaire', 'active' => true, 'boutique_id' => $this->boutique()->id]);

        $this->actingAs($gestionnaire)->get('/produits-import')->assertOk();
    }

    private function produit(Boutique $boutique): Produit
    {
        $categorie = Categorie::create(['nom' => 'Catégorie test', 'active' => true]);

        return Produit::create([
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
    }

    public function test_un_caissier_ne_peut_pas_modifier_ni_desactiver_un_produit(): void
    {
        $boutique = $this->boutique();
        $caissier = User::factory()->create(['role' => 'caissier', 'active' => true, 'boutique_id' => $boutique->id]);
        $produit = $this->produit($boutique);

        $this->actingAs($caissier)->get(route('produits.edit', $produit))->assertForbidden();
        $this->actingAs($caissier)->put(route('produits.update', $produit), [
            'nom' => 'Modifié', 'prix_achat' => 1, 'prix_vente' => 1, 'categorie_id' => $produit->categorie_id,
        ])->assertForbidden();
        $this->actingAs($caissier)->delete(route('produits.destroy', $produit))->assertForbidden();
        $this->actingAs($caissier)->get(route('produits.import'))->assertForbidden();

        $this->assertTrue($produit->fresh()->active);
    }

    public function test_un_gestionnaire_peut_modifier_et_desactiver_un_produit(): void
    {
        $boutique = $this->boutique();
        $gestionnaire = User::factory()->create(['role' => 'gestionnaire', 'active' => true, 'boutique_id' => $boutique->id]);
        $produit = $this->produit($boutique);

        $this->actingAs($gestionnaire)->put(route('produits.update', $produit), [
            'nom' => 'Modifié', 'prix_achat' => 500, 'prix_vente' => 1200, 'categorie_id' => $produit->categorie_id,
        ])->assertRedirect(route('produits.index'));

        $this->assertEquals(1200, $produit->fresh()->prix_vente);

        $this->actingAs($gestionnaire)->delete(route('produits.destroy', $produit))
            ->assertRedirect(route('produits.index'));

        $this->assertFalse($produit->fresh()->active);
    }

    public function test_un_compte_desactive_est_deconnecte(): void
    {
        $user = User::factory()->create(['role' => 'gerant', 'active' => false, 'boutique_id' => $this->boutique()->id]);

        $this->actingAs($user)->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_un_caissier_peut_acceder_aux_rapports_mais_pas_a_la_caisse(): void
    {
        $caissier = User::factory()->create(['role' => 'caissier', 'active' => true, 'boutique_id' => $this->boutique()->id]);

        $this->actingAs($caissier)->get('/rapports')->assertOk();
        $this->actingAs($caissier)->get('/caisse')->assertForbidden();
    }

    public function test_un_caissier_ne_peut_pas_gerer_les_categories(): void
    {
        $caissier = User::factory()->create(['role' => 'caissier', 'active' => true, 'boutique_id' => $this->boutique()->id]);

        $this->actingAs($caissier)->get('/categories')->assertForbidden();
    }
}
