<?php

namespace App\Console\Commands;

use App\Services\SauvegardeService;
use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    protected $signature   = 'backup:database';
    protected $description = 'Sauvegarde la base de données MySQL dans storage/app/backups/';

    public function handle(SauvegardeService $sauvegardes): int
    {
        try {
            $fichier = $sauvegardes->creer();
            $this->info("Sauvegarde créée avec succès : {$fichier}");
            return 0;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return 1;
        }
    }
}
