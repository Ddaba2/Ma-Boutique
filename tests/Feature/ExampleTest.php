<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_la_racine_redirige_vers_le_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('dashboard'));
    }

    public function test_la_page_de_connexion_est_accessible(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
    }

    public function test_le_login_est_limite_apres_plusieurs_tentatives(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->post('/login', ['username' => 'inconnu@test.com', 'password' => 'mauvais']);
        }

        $this->post('/login', ['username' => 'inconnu@test.com', 'password' => 'mauvais'])
            ->assertStatus(429);
    }
}
