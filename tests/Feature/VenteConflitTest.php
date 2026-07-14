<?php

namespace Tests\Feature;

use App\Models\Boutique;
use App\Models\Categorie;
use App\Models\Produit;
use App\Models\User;
use App\Models\Vente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VenteConflitTest extends TestCase
{
    use RefreshDatabase;

    private function venteEnConflit(Boutique $boutique): Vente
    {
        $categorie = Categorie::firstOrCreate(['nom' => 'Catégorie test'], ['active' => true]);
        $produit = Produit::create([
            'reference' => Produit::generateReference(),
            'nom' => 'Produit test',
            'prix_achat' => 500,
            'prix_vente' => 1000,
            'stock_actuel' => 0,
            'stock_min' => 2,
            'stock_max' => 100,
            'categorie_id' => $categorie->id,
            'active' => true,
        ]);

        return Vente::create([
            'reference' => Vente::generateReference(),
            'boutique_id' => $boutique->id,
            'total' => 1000,
            'montant_recu' => 1000,
            'monnaie' => 0,
            'client_nom' => 'Client Test',
            'mode_paiement' => 'espece',
            'statut' => 'terminee',
            'conflit_stock' => true,
            'notes_conflit' => $produit->nom . ' : demandé 2, disponible 0',
        ]);
    }

    public function test_un_caissier_ne_peut_pas_voir_les_conflits(): void
    {
        $boutique = Boutique::create(['nom' => 'Boutique test', 'active' => true]);
        $caissier = User::factory()->create(['role' => 'caissier', 'active' => true, 'boutique_id' => $boutique->id]);

        $this->actingAs($caissier)->get('/ventes-conflits')->assertForbidden();
    }

    public function test_un_gestionnaire_voit_la_liste_des_conflits(): void
    {
        $boutique = Boutique::create(['nom' => 'Boutique test', 'active' => true]);
        $vente = $this->venteEnConflit($boutique);
        $gestionnaire = User::factory()->create(['role' => 'gestionnaire', 'active' => true, 'boutique_id' => $boutique->id]);

        $this->actingAs($gestionnaire)->get('/ventes-conflits')
            ->assertOk()
            ->assertSee($vente->reference);
    }

    public function test_resoudre_un_conflit_le_retire_de_la_liste(): void
    {
        $boutique = Boutique::create(['nom' => 'Boutique test', 'active' => true]);
        $vente = $this->venteEnConflit($boutique);
        $gerant = User::factory()->create(['role' => 'gerant', 'active' => true, 'boutique_id' => $boutique->id]);

        $this->actingAs($gerant)->post("/ventes/{$vente->id}/resoudre-conflit")
            ->assertRedirect(route('ventes.conflits'));

        $this->assertFalse($vente->fresh()->conflit_stock);

        // Un premier GET consomme le message flash de succès (qui mentionne
        // légitimement la référence) ; le second reflète l'état réel de la liste.
        $this->actingAs($gerant)->get('/ventes-conflits');
        $this->actingAs($gerant)->get('/ventes-conflits')->assertDontSee($vente->reference);
    }
}
