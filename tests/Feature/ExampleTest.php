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
}
