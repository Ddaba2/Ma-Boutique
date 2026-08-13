<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Sauvegarde automatique chaque nuit à 02h00
\Illuminate\Support\Facades\Schedule::command('backup:database')->dailyAt('02:00');

// Alerte email de stock bas chaque matin à 07h00
\Illuminate\Support\Facades\Schedule::command('alertes:stock')->dailyAt('07:00');
