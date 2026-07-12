<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    protected $signature   = 'backup:database';
    protected $description = 'Sauvegarde la base de données MySQL dans storage/app/backups/';

    public function handle(): int
    {
        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $host     = config('database.connections.mysql.host');
        $port     = config('database.connections.mysql.port');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $filename  = 'backup_' . $database . '_' . now()->format('Y-m-d_H-i-s') . '.sql';
        $filepath  = $backupDir . DIRECTORY_SEPARATOR . $filename;

        // Chercher mysqldump dans XAMPP ou le PATH
        $mysqldump = $this->findMysqldump();

        if (!$mysqldump) {
            $this->error('mysqldump introuvable. Vérifiez que XAMPP est installé.');
            return 1;
        }

        $passwordArg = $password ? '-p' . escapeshellarg($password) : '';
        $cmd = sprintf(
            '"%s" -h %s -P %s -u %s %s %s > "%s" 2>&1',
            $mysqldump,
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            $passwordArg,
            escapeshellarg($database),
            $filepath
        );

        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($filepath) || filesize($filepath) < 100) {
            $this->error('Échec de la sauvegarde.');
            return 1;
        }

        $this->info("Sauvegarde créée avec succès : $filename");

        // Garder seulement les 7 dernières sauvegardes
        $files = glob($backupDir . DIRECTORY_SEPARATOR . '*.sql');
        if (count($files) > 7) {
            usort($files, fn($a, $b) => strcmp($a, $b));
            foreach (array_slice($files, 0, count($files) - 7) as $old) {
                unlink($old);
                $this->info('Ancienne sauvegarde supprimée : ' . basename($old));
            }
        }

        return 0;
    }

    private function findMysqldump(): ?string
    {
        $candidates = [
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\wamp64\\bin\\mysql\\mysql8.0.31\\bin\\mysqldump.exe',
            'C:\\wamp\\bin\\mysql\\mysql8.0.31\\bin\\mysqldump.exe',
            'mysqldump',
        ];

        foreach ($candidates as $path) {
            if (str_ends_with($path, '.exe')) {
                if (file_exists($path)) return $path;
            } else {
                exec('where ' . escapeshellarg($path) . ' 2>NUL', $out, $code);
                if ($code === 0 && !empty($out)) return trim($out[0]);
            }
        }

        return null;
    }
}
