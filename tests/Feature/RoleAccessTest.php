<?php

namespace Tests\Feature;

use App\Models\Boutique;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private function boutique(): Boutique
    {
        return Boutique::create(['nom' => 'Boutique test', 'active' => true]);
    }

    public function test_un_visiteur_non_connecte_est_redirige_vers_le_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_un_caissier_ne_peut_pas_acceder_a_la_gestion_des_utilisateurs(): void
    {
        $caissier = User::factory()->create(['role' => 'caissier', 'active' => true, 'boutique_id' => $this->boutique()->id]);

        $this->actingAs($caissier)->get('/utilisateurs')->assertForbidden();
    }

    public function test_un_caissier_ne_peut_pas_acceder_aux_fournisseurs(): void
    {
        $caissier = User::factory()->create(['role' => 'caissier', 'active' => true, 'boutique_id' => $this->boutique()->id]);

        $this->actingAs($caissier)->get('/fournisseurs')->assertForbidden();
    }

    public function test_un_gestionnaire_peut_acceder_aux_fournisseurs_mais_pas_aux_utilisateurs(): void
    {
        $boutique = $this->boutique();
        $gestionnaire = User::factory()->create(['role' => 'gestionnaire', 'active' => true, 'boutique_id' => $boutique->id]);

        $this->actingAs($gestionnaire)->get('/fournisseurs')->assertOk();
        $this->actingAs($gestionnaire)->get('/utilisateurs')->assertForbidden();
    }

    public function test_un_gerant_peut_acceder_a_la_gestion_des_utilisateurs(): void
    {
        $gerant = User::factory()->create(['role' => 'gerant', 'active' => true, 'boutique_id' => $this->boutique()->id]);

        $this->actingAs($gerant)->get('/utilisateurs')->assertOk();
    }

    public function test_un_compte_desactive_est_deconnecte(): void
    {
        $user = User::factory()->create(['role' => 'gerant', 'active' => false, 'boutique_id' => $this->boutique()->id]);

        $this->actingAs($user)->get('/fournisseurs')->assertRedirect(route('login'));
    }
}
