<?php

namespace Tests\Feature;

use App\Mail\AlerteStockMail;
use App\Models\Boutique;
use App\Models\BoutiqueProduit;
use App\Models\Categorie;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AlerteStockTest extends TestCase
{
    use RefreshDatabase;

    private function produitAvecStock(Boutique $boutique, int $stockActuel, int $stockMin = 5): Produit
    {
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

        BoutiqueProduit::create([
            'boutique_id' => $boutique->id,
            'produit_id' => $produit->id,
            'stock_actuel' => $stockActuel,
            'stock_min' => $stockMin,
            'stock_max' => 100,
        ]);

        return $produit;
    }

    public function test_envoie_une_alerte_aux_gerants_actifs_si_du_stock_est_bas(): void
    {
        Mail::fake();

        $boutique = Boutique::create(['nom' => 'Boutique test', 'active' => true]);
        $this->produitAvecStock($boutique, 0); // rupture
        $this->produitAvecStock($boutique, 2, 5); // faible

        $gerant = User::factory()->create(['role' => 'gerant', 'active' => true, 'boutique_id' => $boutique->id]);
        $gerantInactif = User::factory()->create(['role' => 'gerant', 'active' => false, 'boutique_id' => $boutique->id]);
        $caissier = User::factory()->create(['role' => 'caissier', 'active' => true, 'boutique_id' => $boutique->id]);

        $this->artisan('alertes:stock')->assertSuccessful();

        Mail::assertQueued(AlerteStockMail::class, function ($mail) use ($boutique) {
            return $mail->boutique->id === $boutique->id
                && $mail->enRupture->count() === 1
                && $mail->stockFaible->count() === 1;
        });

        Mail::assertQueued(AlerteStockMail::class, 1);
    }

    public function test_naucune_alerte_nest_envoyee_si_le_stock_est_normal(): void
    {
        Mail::fake();

        $boutique = Boutique::create(['nom' => 'Boutique test', 'active' => true]);
        $this->produitAvecStock($boutique, 50, 5);

        User::factory()->create(['role' => 'gerant', 'active' => true, 'boutique_id' => $boutique->id]);

        $this->artisan('alertes:stock')->assertSuccessful();

        Mail::assertNothingQueued();
    }

    public function test_les_boutiques_sont_traitees_independamment(): void
    {
        Mail::fake();

        $boutiqueA = Boutique::create(['nom' => 'Boutique A', 'active' => true]);
        $boutiqueB = Boutique::create(['nom' => 'Boutique B', 'active' => true]);

        $categorie = Categorie::create(['nom' => 'Catégorie test', 'active' => true]);
        $produit = Produit::create([
            'reference' => Produit::generateReference(),
            'nom' => 'Produit partagé',
            'prix_achat' => 100,
            'prix_vente' => 200,
            'stock_actuel' => 50,
            'stock_min' => 5,
            'stock_max' => 100,
            'categorie_id' => $categorie->id,
            'active' => true,
        ]);

        // Boutique A en rupture, boutique B en stock normal pour le même produit.
        BoutiqueProduit::create(['boutique_id' => $boutiqueA->id, 'produit_id' => $produit->id, 'stock_actuel' => 0, 'stock_min' => 5, 'stock_max' => 100]);
        BoutiqueProduit::create(['boutique_id' => $boutiqueB->id, 'produit_id' => $produit->id, 'stock_actuel' => 50, 'stock_min' => 5, 'stock_max' => 100]);

        User::factory()->create(['role' => 'gerant', 'active' => true, 'boutique_id' => $boutiqueA->id]);

        $this->artisan('alertes:stock')->assertSuccessful();

        Mail::assertQueued(AlerteStockMail::class, function ($mail) use ($boutiqueA) {
            return $mail->boutique->id === $boutiqueA->id;
        });
        Mail::assertQueued(AlerteStockMail::class, 1);
    }
}
