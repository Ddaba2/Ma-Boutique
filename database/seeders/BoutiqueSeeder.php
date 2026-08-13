<?php

namespace Database\Seeders;

use App\Models\Boutique;
use Illuminate\Database\Seeder;

class BoutiqueSeeder extends Seeder
{
    public function run(): void
    {
        Boutique::firstOrCreate(
            ['nom' => 'Boutique principale'],
            ['active' => true]
        );
    }
}
