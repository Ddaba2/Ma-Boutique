<?php

namespace Tests\Feature;

use App\Models\Boutique;
use App\Models\Categorie;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProduitImportTest extends TestCase
{
    use RefreshDatabase;

    private function gestionnaire(): User
    {
        $boutique = Boutique::create(['nom' => 'Boutique test', 'active' => true]);

        return User::factory()->create(['role' => 'gestionnaire', 'active' => true, 'boutique_id' => $boutique->id]);
    }

    public function test_une_ligne_avec_un_nom_valide_est_importee(): void
    {
        Categorie::create(['nom' => 'Catégorie test', 'active' => true]);

        $csv = "nom;description;prix_achat;prix_vente;stock_actuel;stock_min;stock_max;categorie\n"
            ."Savon;Savon de Marseille;500;1000;20;5;100;Catégorie test\n";
        $fichier = UploadedFile::fake()->createWithContent('produits.csv', $csv);

        $response = $this->actingAs($this->gestionnaire())
            ->post(route('produits.import.csv'), ['fichier_csv' => $fichier]);

        $response->assertRedirect(route('produits.index'));
        $response->assertSessionHas('success');
        $response->assertSessionMissing('warning');
        $this->assertSame(1, Produit::where('nom', 'Savon')->count());
    }

    public function test_une_ligne_avec_un_nom_contenant_des_chevrons_est_ignoree_et_signalee(): void
    {
        Categorie::create(['nom' => 'Catégorie test', 'active' => true]);

        $csv = "nom;description;prix_achat;prix_vente;stock_actuel;stock_min;stock_max;categorie\n"
            ."<script>alert(1)</script>;desc;500;1000;20;5;100;Catégorie test\n"
            ."Savon;Savon de Marseille;500;1000;20;5;100;Catégorie test\n";
        $fichier = UploadedFile::fake()->createWithContent('produits.csv', $csv);

        $response = $this->actingAs($this->gestionnaire())
            ->post(route('produits.import.csv'), ['fichier_csv' => $fichier]);

        $response->assertRedirect(route('produits.index'));
        $response->assertSessionHas('warning');
        $this->assertSame(1, Produit::count());
        $this->assertSame(1, Produit::where('nom', 'Savon')->count());
    }
}
