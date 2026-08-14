<?php

namespace Tests\Feature;

use App\Models\Boutique;
use App\Models\Categorie;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategorieTest extends TestCase
{
    use RefreshDatabase;

    private function gerant(): User
    {
        $boutique = Boutique::create(['nom' => 'Boutique test', 'active' => true]);

        return User::factory()->create(['role' => 'gerant', 'active' => true, 'boutique_id' => $boutique->id]);
    }

    public function test_index_est_accessible(): void
    {
        $user = $this->gerant();
        $this->actingAs($user)->get('/categories')->assertOk();
    }

    public function test_show_resout_le_bon_modele(): void
    {
        $user = $this->gerant();
        $categorie = Categorie::create(['nom' => 'Boissons', 'active' => true]);

        $this->actingAs($user)->get("/categories/{$categorie->id}")
            ->assertOk()
            ->assertSee('Boissons');
    }

    public function test_update_modifie_la_bonne_categorie(): void
    {
        $user = $this->gerant();
        $categorie = Categorie::create(['nom' => 'Boissons', 'active' => true]);

        $this->actingAs($user)->put("/categories/{$categorie->id}", [
            'nom' => 'Boissons fraiches',
            'active' => 1,
        ])->assertRedirect(route('categories.index'));

        $this->assertSame('Boissons fraiches', $categorie->fresh()->nom);
    }

    public function test_store_cree_une_categorie(): void
    {
        $user = $this->gerant();

        $this->actingAs($user)->post('/categories', [
            'nom' => 'Électronique',
            'active' => 1,
        ])->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', ['nom' => 'Électronique']);
    }

    public function test_destroy_refuse_si_des_produits_sont_associes(): void
    {
        $user = $this->gerant();
        $categorie = Categorie::create(['nom' => 'Boissons', 'active' => true]);

        Produit::create([
            'reference' => Produit::generateReference(),
            'nom' => 'Coca',
            'prix_achat' => 100,
            'prix_vente' => 200,
            'stock_actuel' => 5,
            'stock_min' => 1,
            'stock_max' => 50,
            'categorie_id' => $categorie->id,
            'active' => true,
        ]);

        $this->actingAs($user)->delete("/categories/{$categorie->id}")
            ->assertRedirect();

        $this->assertDatabaseHas('categories', ['id' => $categorie->id]);
    }
}
