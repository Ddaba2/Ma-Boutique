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

class VenteSyncTest extends TestCase
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

    private function produit(Boutique $boutique, int $stockActuel = 10): Produit
    {
        $categorie = Categorie::firstOrCreate(['nom' => 'Catégorie test'], ['active' => true]);

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

    public function test_le_rafraichissement_csrf_retourne_un_jeton(): void
    {
        $boutique = $this->boutique();
        $user = $this->caissier($boutique);

        $response = $this->actingAs($user)->getJson('/api/csrf-refresh');

        $response->assertOk()->assertJsonStructure(['token']);
    }

    public function test_synchronise_une_vente_hors_ligne_avec_succes(): void
    {
        $boutique = $this->boutique();
        $user = $this->caissier($boutique);
        $produit = $this->produit($boutique, 10);

        $response = $this->actingAs($user)->postJson('/api/ventes/sync', [
            'uuid_client' => 'uuid-test-1',
            'client_nom' => 'Client Offline',
            'mode_paiement' => 'espece',
            'montant_recu' => 3000,
            'lignes' => [
                ['produit_id' => $produit->id, 'quantite' => 2, 'prix_unitaire' => 1000],
            ],
        ]);

        $response->assertCreated()->assertJson(['synced' => true, 'conflit' => false]);

        $vente = Vente::where('uuid_client', 'uuid-test-1')->first();
        $this->assertNotNull($vente);
        $this->assertEquals(2000, $vente->total);

        $stock = BoutiqueProduit::withoutGlobalScopes()->where('boutique_id', $boutique->id)->where('produit_id', $produit->id)->first();
        $this->assertSame(8, $stock->stock_actuel);
    }

    public function test_rejouer_la_meme_synchronisation_ne_duplique_pas_la_vente(): void
    {
        $boutique = $this->boutique();
        $user = $this->caissier($boutique);
        $produit = $this->produit($boutique, 10);

        $payload = [
            'uuid_client' => 'uuid-test-2',
            'client_nom' => 'Client Offline',
            'mode_paiement' => 'espece',
            'montant_recu' => 2000,
            'lignes' => [
                ['produit_id' => $produit->id, 'quantite' => 1, 'prix_unitaire' => 1000],
            ],
        ];

        $this->actingAs($user)->postJson('/api/ventes/sync', $payload)->assertCreated();
        $second = $this->actingAs($user)->postJson('/api/ventes/sync', $payload);

        $second->assertOk()->assertJson(['synced' => true, 'deja_synchronisee' => true]);
        $this->assertSame(1, Vente::where('uuid_client', 'uuid-test-2')->count());

        $stock = BoutiqueProduit::withoutGlobalScopes()->where('boutique_id', $boutique->id)->where('produit_id', $produit->id)->first();
        $this->assertSame(9, $stock->stock_actuel, 'Le stock ne doit être décrémenté qu\'une seule fois.');
    }

    public function test_une_synchronisation_avec_stock_insuffisant_est_marquee_en_conflit_plutot_que_rejetee(): void
    {
        $boutique = $this->boutique();
        $user = $this->caissier($boutique);
        $produit = $this->produit($boutique, 1);

        $response = $this->actingAs($user)->postJson('/api/ventes/sync', [
            'uuid_client' => 'uuid-test-3',
            'client_nom' => 'Client Offline',
            'mode_paiement' => 'espece',
            'montant_recu' => 5000,
            'lignes' => [
                ['produit_id' => $produit->id, 'quantite' => 5, 'prix_unitaire' => 1000],
            ],
        ]);

        $response->assertCreated()->assertJson(['synced' => true, 'conflit' => true]);

        $vente = Vente::where('uuid_client', 'uuid-test-3')->first();
        $this->assertNotNull($vente);
        $this->assertTrue($vente->conflit_stock);
        $this->assertNotNull($vente->notes_conflit);

        $stock = BoutiqueProduit::withoutGlobalScopes()->where('boutique_id', $boutique->id)->where('produit_id', $produit->id)->first();
        $this->assertSame(0, $stock->stock_actuel, 'Le stock ne doit jamais devenir négatif.');
    }
}
