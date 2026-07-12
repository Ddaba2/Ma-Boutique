<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\Produit;
use App\Models\User;
use App\Models\Vente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VenteTest extends TestCase
{
    use RefreshDatabase;

    private function produit(array $attributes = []): Produit
    {
        $categorie = Categorie::create([
            'nom' => 'Catégorie test',
            'active' => true,
        ]);

        return Produit::create(array_merge([
            'reference' => Produit::generateReference(),
            'nom' => 'Produit test',
            'prix_achat' => 500,
            'prix_vente' => 1000,
            'stock_actuel' => 10,
            'stock_min' => 2,
            'stock_max' => 100,
            'categorie_id' => $categorie->id,
            'active' => true,
        ], $attributes));
    }

    public function test_une_vente_decremente_le_stock_et_calcule_la_monnaie(): void
    {
        $user = User::factory()->create(['role' => 'caissier', 'active' => true]);
        $produit = $this->produit();

        $response = $this->actingAs($user)->post('/ventes', [
            'client_nom' => 'Client Test',
            'mode_paiement' => 'espece',
            'montant_recu' => 3000,
            'produit_ids' => [$produit->id],
            'quantites' => [2],
            'prix_unitaires' => [1000],
        ]);

        $response->assertRedirect(route('ventes.index'));

        $produit->refresh();
        $this->assertSame(8, $produit->stock_actuel);

        $vente = Vente::first();
        $this->assertNotNull($vente);
        $this->assertEquals(2000, $vente->total);
        $this->assertEquals(1000, $vente->monnaie);
    }

    public function test_une_vente_est_refusee_si_le_stock_est_insuffisant(): void
    {
        $user = User::factory()->create(['role' => 'caissier', 'active' => true]);
        $produit = $this->produit(['stock_actuel' => 1]);

        $this->actingAs($user)->post('/ventes', [
            'client_nom' => 'Client Test',
            'mode_paiement' => 'espece',
            'montant_recu' => 5000,
            'produit_ids' => [$produit->id],
            'quantites' => [5],
            'prix_unitaires' => [1000],
        ]);

        $produit->refresh();
        $this->assertSame(1, $produit->stock_actuel);
        $this->assertSame(0, Vente::count());
    }
}
