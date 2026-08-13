<?php

namespace Tests\Feature;

use App\Models\Boutique;
use App\Models\BoutiqueProduit;
use App\Models\Categorie;
use App\Models\Produit;
use App\Models\User;
use App\Models\Vente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VenteTest extends TestCase
{
    use RefreshDatabase;

    private function boutique(): Boutique
    {
        return Boutique::create(['nom' => 'Boutique test', 'active' => true]);
    }

    private function caissier(Boutique $boutique): User
    {
        return User::factory()->create(['role' => 'caissier', 'active' => true, 'boutique_id' => $boutique->id]);
    }

    private function gerant(Boutique $boutique): User
    {
        return User::factory()->create(['role' => 'gerant', 'active' => true, 'boutique_id' => $boutique->id]);
    }

    private function produit(Boutique $boutique, int $stockActuel = 10): Produit
    {
        $categorie = Categorie::create([
            'nom' => 'Catégorie test',
            'active' => true,
        ]);

        $produit = Produit::create([
            'reference' => Produit::generateReference(),
            'nom' => 'Produit test',
            'prix_achat' => 500,
            'prix_vente' => 1000,
            'stock_actuel' => $stockActuel,
            'stock_min' => 2,
            'stock_max' => 100,
            'categorie_id' => $categorie->id,
            'active' => true,
        ]);

        BoutiqueProduit::create([
            'boutique_id' => $boutique->id,
            'produit_id' => $produit->id,
            'stock_actuel' => $stockActuel,
            'stock_min' => 2,
            'stock_max' => 100,
        ]);

        return $produit;
    }

    public function test_une_vente_decremente_le_stock_et_calcule_la_monnaie(): void
    {
        $boutique = $this->boutique();
        $user = $this->caissier($boutique);
        $produit = $this->produit($boutique);

        $response = $this->actingAs($user)->post('/ventes', [
            'client_nom' => 'Client Test',
            'mode_paiement' => 'espece',
            'montant_recu' => 3000,
            'produit_ids' => [$produit->id],
            'quantites' => [2],
            'prix_unitaires' => [1000],
        ]);

        $response->assertRedirect(route('ventes.index'));

        $stock = BoutiqueProduit::withoutGlobalScopes()->where('boutique_id', $boutique->id)->where('produit_id', $produit->id)->first();
        $this->assertSame(8, $stock->stock_actuel);

        $vente = Vente::first();
        $this->assertNotNull($vente);
        $this->assertSame($boutique->id, $vente->boutique_id);
        $this->assertEquals(2000, $vente->total);
        $this->assertEquals(1000, $vente->monnaie);
    }

    public function test_une_vente_est_refusee_si_le_stock_est_insuffisant(): void
    {
        $boutique = $this->boutique();
        $user = $this->caissier($boutique);
        $produit = $this->produit($boutique, 1);

        $this->actingAs($user)->post('/ventes', [
            'client_nom' => 'Client Test',
            'mode_paiement' => 'espece',
            'montant_recu' => 5000,
            'produit_ids' => [$produit->id],
            'quantites' => [5],
            'prix_unitaires' => [1000],
        ]);

        $stock = BoutiqueProduit::withoutGlobalScopes()->where('boutique_id', $boutique->id)->where('produit_id', $produit->id)->first();
        $this->assertSame(1, $stock->stock_actuel);
        $this->assertSame(0, Vente::count());
    }

    public function test_une_vente_accepte_le_mobile_money_comme_mode_de_paiement(): void
    {
        $boutique = $this->boutique();
        $user = $this->caissier($boutique);
        $produit = $this->produit($boutique);

        $this->actingAs($user)->post('/ventes', [
            'client_nom' => 'Client Test',
            'mode_paiement' => 'mobile',
            'montant_recu' => 2000,
            'produit_ids' => [$produit->id],
            'quantites' => [2],
            'prix_unitaires' => [1000],
        ])->assertRedirect(route('ventes.index'));

        $this->assertSame('mobile', Vente::first()->mode_paiement);
    }

    public function test_une_vente_refuse_un_mode_de_paiement_invalide(): void
    {
        $boutique = $this->boutique();
        $user = $this->caissier($boutique);
        $produit = $this->produit($boutique);

        $this->actingAs($user)->post('/ventes', [
            'client_nom' => 'Client Test',
            'mode_paiement' => 'mobile_money',
            'montant_recu' => 2000,
            'produit_ids' => [$produit->id],
            'quantites' => [2],
            'prix_unitaires' => [1000],
        ])->assertSessionHasErrors('mode_paiement');

        $this->assertSame(0, Vente::count());
    }

    public function test_le_ticket_de_caisse_saffiche_avec_les_bonnes_informations(): void
    {
        $boutique = $this->boutique();
        $user = $this->caissier($boutique);
        $produit = $this->produit($boutique);

        $this->actingAs($user)->post('/ventes', [
            'client_nom' => 'Client Test',
            'mode_paiement' => 'espece',
            'montant_recu' => 3000,
            'produit_ids' => [$produit->id],
            'quantites' => [2],
            'prix_unitaires' => [1000],
        ]);

        $vente = Vente::first();

        $this->actingAs($user)->get(route('ventes.ticket', $vente))
            ->assertOk()
            ->assertSee($vente->reference)
            ->assertSee('Produit test')
            ->assertSee('2 000');
    }

    public function test_le_stock_dune_autre_boutique_nest_pas_affecte(): void
    {
        $boutiqueA = Boutique::create(['nom' => 'Boutique A', 'active' => true]);
        $boutiqueB = Boutique::create(['nom' => 'Boutique B', 'active' => true]);

        $categorie = Categorie::create(['nom' => 'Catégorie test', 'active' => true]);
        $produit = Produit::create([
            'reference' => Produit::generateReference(),
            'nom' => 'Produit partagé',
            'prix_achat' => 500,
            'prix_vente' => 1000,
            'stock_actuel' => 10,
            'stock_min' => 2,
            'stock_max' => 100,
            'categorie_id' => $categorie->id,
            'active' => true,
        ]);

        BoutiqueProduit::create(['boutique_id' => $boutiqueA->id, 'produit_id' => $produit->id, 'stock_actuel' => 10, 'stock_min' => 2, 'stock_max' => 100]);
        BoutiqueProduit::create(['boutique_id' => $boutiqueB->id, 'produit_id' => $produit->id, 'stock_actuel' => 10, 'stock_min' => 2, 'stock_max' => 100]);

        $caissierA = $this->caissier($boutiqueA);

        $this->actingAs($caissierA)->post('/ventes', [
            'client_nom' => 'Client A',
            'mode_paiement' => 'espece',
            'montant_recu' => 3000,
            'produit_ids' => [$produit->id],
            'quantites' => [3],
            'prix_unitaires' => [1000],
        ])->assertRedirect(route('ventes.index'));

        $stockA = BoutiqueProduit::withoutGlobalScopes()->where('boutique_id', $boutiqueA->id)->where('produit_id', $produit->id)->first();
        $stockB = BoutiqueProduit::withoutGlobalScopes()->where('boutique_id', $boutiqueB->id)->where('produit_id', $produit->id)->first();

        $this->assertSame(7, $stockA->stock_actuel);
        $this->assertSame(10, $stockB->stock_actuel);
        $this->assertSame(1, Vente::withoutGlobalScopes()->count());
    }

    public function test_un_prix_unitaire_different_du_catalogue_est_rejete(): void
    {
        $boutique = $this->boutique();
        $user = $this->caissier($boutique);
        $produit = $this->produit($boutique);

        $this->actingAs($user)->post('/ventes', [
            'client_nom' => 'Client Test',
            'mode_paiement' => 'espece',
            'montant_recu' => 100,
            'produit_ids' => [$produit->id],
            'quantites' => [2],
            'prix_unitaires' => [50], // prix catalogue réel : 1000
        ])->assertSessionHas('error');

        $this->assertSame(0, Vente::count());
    }

    public function test_un_montant_recu_insuffisant_est_refuse(): void
    {
        $boutique = $this->boutique();
        $user = $this->caissier($boutique);
        $produit = $this->produit($boutique);

        $this->actingAs($user)->post('/ventes', [
            'client_nom' => 'Client Test',
            'mode_paiement' => 'espece',
            'montant_recu' => 500,
            'produit_ids' => [$produit->id],
            'quantites' => [2],
            'prix_unitaires' => [1000],
        ])->assertSessionHas('error');

        $this->assertSame(0, Vente::count());
    }

    public function test_un_gerant_peut_annuler_une_vente_et_le_stock_est_restitue(): void
    {
        $boutique = $this->boutique();
        $caissier = $this->caissier($boutique);
        $gerant = $this->gerant($boutique);
        $produit = $this->produit($boutique, 10);

        $this->actingAs($caissier)->post('/ventes', [
            'client_nom' => 'Client Test',
            'mode_paiement' => 'espece',
            'montant_recu' => 3000,
            'produit_ids' => [$produit->id],
            'quantites' => [2],
            'prix_unitaires' => [1000],
        ]);

        $vente = Vente::first();
        $stockApresVente = BoutiqueProduit::withoutGlobalScopes()->where('boutique_id', $boutique->id)->where('produit_id', $produit->id)->first();
        $this->assertSame(8, $stockApresVente->stock_actuel);

        $this->actingAs($gerant)->delete("/ventes/{$vente->id}")
            ->assertRedirect(route('ventes.index'));

        $vente->refresh();
        $this->assertSame('annulee', $vente->statut);
        $this->assertSame(1, Vente::count(), 'la vente annulée doit toujours exister, pas être supprimée');

        $stockApresAnnulation = BoutiqueProduit::withoutGlobalScopes()->where('boutique_id', $boutique->id)->where('produit_id', $produit->id)->first();
        $this->assertSame(10, $stockApresAnnulation->stock_actuel);

        $this->assertDatabaseHas('mouvements_stock', [
            'produit_id' => $produit->id,
            'type' => 'retour_client',
            'quantite' => 2,
        ]);
    }

    public function test_un_caissier_ne_peut_pas_annuler_une_vente(): void
    {
        $boutique = $this->boutique();
        $caissier = $this->caissier($boutique);
        $produit = $this->produit($boutique, 10);

        $this->actingAs($caissier)->post('/ventes', [
            'client_nom' => 'Client Test',
            'mode_paiement' => 'espece',
            'montant_recu' => 3000,
            'produit_ids' => [$produit->id],
            'quantites' => [2],
            'prix_unitaires' => [1000],
        ]);

        $vente = Vente::first();

        $this->actingAs($caissier)->delete("/ventes/{$vente->id}")->assertForbidden();
        $this->assertSame('terminee', $vente->fresh()->statut);
    }

    public function test_une_vente_deja_annulee_ne_peut_pas_etre_annulee_a_nouveau(): void
    {
        $boutique = $this->boutique();
        $gerant = $this->gerant($boutique);
        $produit = $this->produit($boutique, 10);

        $vente = Vente::create([
            'reference' => Vente::generateReference(),
            'boutique_id' => $boutique->id,
            'total' => 1000,
            'montant_recu' => 1000,
            'monnaie' => 0,
            'client_nom' => 'Client Test',
            'mode_paiement' => 'espece',
            'statut' => 'annulee',
        ]);

        $this->actingAs($gerant)->delete("/ventes/{$vente->id}")
            ->assertRedirect(route('ventes.index'));

        $this->assertDatabaseCount('mouvements_stock', 0);
    }
}
