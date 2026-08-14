<?php

namespace Tests\Unit;

use App\Models\Boutique;
use App\Models\BoutiqueProduit;
use App\Models\Categorie;
use App\Models\Produit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Le statut de stock ('rupture'/'faible'/'normal') est porté par
 * BoutiqueProduit (stock réel, par boutique), pas par les colonnes
 * stock_actuel/stock_min sur Produit — celles-ci ne servent qu'à amorcer la
 * première ligne de stock à la création et ne sont plus mises à jour ensuite
 * (voir App\Models\Concerns\BelongsToBoutique et App\Models\BoutiqueProduit).
 */
class ProduitStockStatusTest extends TestCase
{
    use RefreshDatabase;

    private ?Boutique $boutique = null;

    private function boutiqueProduit(int $stockActuel, int $stockMin): BoutiqueProduit
    {
        // Une seule boutique réutilisée pour tous les produits du test : en
        // créer une nouvelle à chaque appel déclenche Boutique::booted(), qui
        // amorce une ligne de stock à 0 pour chaque produit déjà existant et
        // fausse les comptages des scopes ci-dessous.
        $this->boutique ??= Boutique::create(['nom' => 'Boutique test', 'active' => true]);
        $boutique = $this->boutique;
        $categorie = Categorie::firstOrCreate(['nom' => 'Catégorie test'], ['active' => true]);

        $produit = Produit::create([
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

        return BoutiqueProduit::create([
            'boutique_id' => $boutique->id,
            'produit_id' => $produit->id,
            'stock_actuel' => $stockActuel,
            'stock_min' => $stockMin,
            'stock_max' => 100,
        ]);
    }

    public function test_stock_a_zero_est_en_rupture(): void
    {
        $this->assertSame('rupture', $this->boutiqueProduit(0, 5)->statutStock());
    }

    public function test_stock_sous_le_seuil_minimum_est_faible(): void
    {
        $this->assertSame('faible', $this->boutiqueProduit(3, 5)->statutStock());
    }

    public function test_stock_au_dessus_du_seuil_minimum_est_normal(): void
    {
        $this->assertSame('normal', $this->boutiqueProduit(20, 5)->statutStock());
    }

    public function test_les_scopes_filtrent_correctement(): void
    {
        $this->boutiqueProduit(0, 5);
        $this->boutiqueProduit(3, 5);
        $this->boutiqueProduit(20, 5);

        $this->assertSame(1, BoutiqueProduit::enRupture()->count());
        $this->assertSame(1, BoutiqueProduit::stockFaible()->count());
        $this->assertSame(1, BoutiqueProduit::stockNormal()->count());
    }
}
