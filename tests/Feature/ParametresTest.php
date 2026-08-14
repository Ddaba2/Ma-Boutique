<?php

namespace Tests\Feature;

use App\Models\Boutique;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ParametresTest extends TestCase
{
    use RefreshDatabase;

    private function boutique(): Boutique
    {
        return Boutique::create(['nom' => 'Boutique test', 'active' => true]);
    }

    private function gerant(Boutique $boutique): User
    {
        return User::factory()->create(['role' => 'gerant', 'active' => true, 'boutique_id' => $boutique->id]);
    }

    public function test_un_caissier_ne_peut_pas_acceder_aux_parametres(): void
    {
        $boutique = $this->boutique();
        $caissier = User::factory()->create(['role' => 'caissier', 'active' => true, 'boutique_id' => $boutique->id]);

        $this->actingAs($caissier)->get('/parametres')->assertForbidden();
        $this->actingAs($caissier)->get('/utilisateurs/create')->assertForbidden();
    }

    public function test_la_page_parametres_liste_les_utilisateurs_existants(): void
    {
        $boutique = $this->boutique();
        $gerant = $this->gerant($boutique);
        User::factory()->create(['role' => 'caissier', 'name' => 'Aissata Test', 'active' => true, 'boutique_id' => $boutique->id]);

        $this->actingAs($gerant)->get('/parametres')
            ->assertOk()
            ->assertSee('Aissata Test');
    }

    public function test_un_gerant_peut_creer_un_utilisateur(): void
    {
        $boutique = $this->boutique();
        $gerant = $this->gerant($boutique);

        $reponse = $this->actingAs($gerant)->post('/utilisateurs', [
            'name' => 'Nouvelle Caissiere',
            'email' => 'nouvelle.caissiere@test.local',
            'password' => 'motdepasse123',
            'password_confirmation' => 'motdepasse123',
            'role' => 'caissier',
            'boutique_id' => $boutique->id,
        ]);

        $reponse->assertRedirect(route('parametres.index'));

        $cree = User::where('email', 'nouvelle.caissiere@test.local')->first();
        $this->assertNotNull($cree);
        $this->assertSame('caissier', $cree->role);
        $this->assertTrue($cree->active);
        $this->assertTrue(Hash::check('motdepasse123', $cree->password));
    }

    public function test_la_creation_echoue_si_lemail_existe_deja(): void
    {
        $boutique = $this->boutique();
        $gerant = $this->gerant($boutique);
        User::factory()->create(['email' => 'deja@test.local', 'boutique_id' => $boutique->id]);

        $this->actingAs($gerant)->post('/utilisateurs', [
            'name' => 'X',
            'email' => 'deja@test.local',
            'password' => 'motdepasse123',
            'password_confirmation' => 'motdepasse123',
            'role' => 'caissier',
            'boutique_id' => $boutique->id,
        ])->assertSessionHasErrors('email');
    }

    public function test_un_gerant_peut_modifier_le_role_dun_utilisateur(): void
    {
        $boutique = $this->boutique();
        $gerant = $this->gerant($boutique);
        $caissier = User::factory()->create(['role' => 'caissier', 'active' => true, 'boutique_id' => $boutique->id]);

        $this->actingAs($gerant)->put(route('utilisateurs.update', $caissier), [
            'name' => $caissier->name,
            'email' => $caissier->email,
            'role' => 'gestionnaire',
            'boutique_id' => $boutique->id,
            'active' => '1',
        ])->assertRedirect(route('parametres.index'));

        $this->assertSame('gestionnaire', $caissier->fresh()->role);
    }

    public function test_un_gerant_ne_peut_pas_desactiver_son_propre_compte(): void
    {
        $boutique = $this->boutique();
        $gerant = $this->gerant($boutique);

        $this->actingAs($gerant)->delete(route('utilisateurs.destroy', $gerant))
            ->assertRedirect();

        $this->assertTrue($gerant->fresh()->active);
    }

    public function test_un_gerant_peut_desactiver_un_autre_utilisateur(): void
    {
        $boutique = $this->boutique();
        $gerant = $this->gerant($boutique);
        $caissier = User::factory()->create(['role' => 'caissier', 'active' => true, 'boutique_id' => $boutique->id]);

        $this->actingAs($gerant)->delete(route('utilisateurs.destroy', $caissier))
            ->assertRedirect(route('parametres.index'));

        $this->assertFalse($caissier->fresh()->active);
    }

    public function test_une_restauration_sans_mot_de_passe_echoue_la_validation(): void
    {
        $boutique = $this->boutique();
        $gerant = $this->gerant($boutique);

        $this->actingAs($gerant)->post(route('sauvegardes.restaurer'), [])
            ->assertSessionHasErrors('password');
    }

    public function test_une_restauration_avec_un_mauvais_mot_de_passe_est_refusee(): void
    {
        $boutique = $this->boutique();
        $gerant = $this->gerant($boutique);
        $gerant->forceFill(['password' => Hash::make('bonmotdepasse')])->save();

        $this->actingAs($gerant)->post(route('sauvegardes.restaurer'), [
            'password' => 'mauvaismotdepasse',
        ])->assertRedirect(route('parametres.index', ['onglet' => 'sauvegardes']));

        $this->assertSame('Mot de passe incorrect. Restauration annulée.', session('error'));
    }

    public function test_une_restauration_avec_le_bon_mot_de_passe_depasse_la_verification(): void
    {
        $boutique = $this->boutique();
        $gerant = $this->gerant($boutique);
        $gerant->forceFill(['password' => Hash::make('bonmotdepasse')])->save();

        $this->actingAs($gerant)->post(route('sauvegardes.restaurer'), [
            'password' => 'bonmotdepasse',
        ])->assertRedirect(route('parametres.index', ['onglet' => 'sauvegardes']));

        $this->assertNotSame('Mot de passe incorrect. Restauration annulée.', session('error'));
    }
}
