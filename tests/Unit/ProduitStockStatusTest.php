<?php

namespace Tests\Unit;

use App\Models\Categorie;
use App\Models\Produit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProduitStockStatusTest extends TestCase
{
    use RefreshDatabase;

    private function produit(int $stockActuel, int $stockMin): Produit
    {
        $categorie = Categorie::firstOrCreate(['nom' => 'Catégorie test'], ['active' => true]);

        return Produit::create([
            'reference' => Produit::generateReference(),
            'nom' => 'Produit test',
            'prix_achat' => 100,
            'prix_vente' => 200,
            'stock_actuel' => $stockActuel,
            'stock_min' => $stockMin,
            'stock_max' => 100,
            'categorie_id' => $categorie->id,
            'active' => true,
        ]);
    }

    public function test_stock_a_zero_est_en_rupture(): void
    {
        $this->assertSame('rupture', $this->produit(0, 5)->statutStock());
    }

    public function test_stock_sous_le_seuil_minimum_est_faible(): void
    {
        $this->assertSame('faible', $this->produit(3, 5)->statutStock());
    }

    public function test_stock_au_dessus_du_seuil_minimum_est_normal(): void
    {
        $this->assertSame('normal', $this->produit(20, 5)->statutStock());
    }

    public function test_les_scopes_filtrent_correctement(): void
    {
        $this->produit(0, 5);
        $this->produit(3, 5);
        $this->produit(20, 5);

        $this->assertSame(1, Produit::enRupture()->count());
        $this->assertSame(1, Produit::stockFaible()->count());
        $this->assertSame(1, Produit::stockNormal()->count());
    }
}
