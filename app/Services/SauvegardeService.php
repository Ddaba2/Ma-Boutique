<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * Sauvegarde/restauration de la base MySQL via mysqldump/mysql en ligne de
 * commande. Les fichiers produits sont des dumps SQL standards (schéma +
 * données, --add-drop-table), donc importables tels quels depuis phpMyAdmin
 * si jamais la restauration depuis l'appli n'est pas possible.
 */
class SauvegardeService
{
    private const MOTIF_NOM_FICHIER = '/^(backup|avant_restauration)_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.sql$/';

    /**
     * @return array<int, array{nom: string, date: Carbon, taille_ko: float}>
     */
    public function lister(): array
    {
        $fichiers = glob($this->repertoire() . DIRECTORY_SEPARATOR . '*.sql') ?: [];
        rsort($fichiers); // le nom contient un horodatage : tri alphabétique = tri chronologique

        return array_map(fn (string $chemin) => [
            'nom'       => basename($chemin),
            'date'      => Carbon::createFromTimestamp(filemtime($chemin)),
            'taille_ko' => round(filesize($chemin) / 1024, 1),
        ], $fichiers);
    }

    public function derniere(): ?string
    {
        return $this->lister()[0]['nom'] ?? null;
    }

    /**
     * @throws \RuntimeException si mysqldump est introuvable ou échoue
     */
    public function creer(string $prefixe = 'backup'): string
    {
        $this->assurerRepertoire();

        $mysqldump = $this->trouverBinaire('mysqldump');
        if (!$mysqldump) {
            throw new \RuntimeException(
                "mysqldump introuvable. Vérifiez votre installation MySQL (XAMPP/WAMP) ou définissez ".
                "SAUVEGARDE_MYSQL_BIN_PATH dans le fichier .env."
            );
        }

        $config     = config('database.connections.mysql');
        $nomFichier = $prefixe . '_' . now()->format('Y-m-d_H-i-s') . '.sql';
        $chemin     = $this->repertoire() . DIRECTORY_SEPARATOR . $nomFichier;

        // Le mot de passe passe par une variable d'environnement plutôt que par
        // l'argument -p en ligne de commande, pour ne pas rester visible en
        // clair dans le gestionnaire des tâches pendant l'exécution.
        if (!empty($config['password'])) {
            putenv('MYSQL_PWD=' . $config['password']);
        }

        $commande = sprintf(
            '"%s" --add-drop-table -h %s -P %s -u %s %s > "%s" 2>&1',
            $mysqldump,
            escapeshellarg($config['host']),
            escapeshellarg((string) $config['port']),
            escapeshellarg($config['username']),
            escapeshellarg($config['database']),
            $chemin
        );

        exec($commande, $sortie, $code);

        if (!empty($config['password'])) {
            putenv('MYSQL_PWD');
        }

        if ($code !== 0 || !file_exists($chemin) || filesize($chemin) < 50) {
            if (file_exists($chemin)) {
                unlink($chemin);
            }
            throw new \RuntimeException('Échec de la sauvegarde : ' . implode(' ', $sortie));
        }

        $this->purgerAnciennes();

        return $nomFichier;
    }

    /**
     * Restaure la base à partir du fichier indiqué. Crée d'abord une
     * sauvegarde de sécurité de l'état actuel, pour pouvoir annuler une
     * restauration déclenchée par erreur.
     *
     * @throws \RuntimeException si le fichier est invalide, mysql est
     *   introuvable, ou la restauration échoue
     */
    public function restaurer(string $nomFichier): void
    {
        $chemin = $this->cheminSecurise($nomFichier);
        if (!$chemin) {
            throw new \RuntimeException("Fichier de sauvegarde introuvable ou invalide : {$nomFichier}");
        }

        $mysql = $this->trouverBinaire('mysql');
        if (!$mysql) {
            throw new \RuntimeException(
                "Le client mysql est introuvable. Vérifiez votre installation MySQL (XAMPP/WAMP) ou définissez ".
                "SAUVEGARDE_MYSQL_BIN_PATH dans le fichier .env."
            );
        }

        $this->creer('avant_restauration');

        $config = config('database.connections.mysql');

        if (!empty($config['password'])) {
            putenv('MYSQL_PWD=' . $config['password']);
        }

        $commande = sprintf(
            '"%s" -h %s -P %s -u %s %s < "%s" 2>&1',
            $mysql,
            escapeshellarg($config['host']),
            escapeshellarg((string) $config['port']),
            escapeshellarg($config['username']),
            escapeshellarg($config['database']),
            $chemin
        );

        exec($commande, $sortie, $code);

        if (!empty($config['password'])) {
            putenv('MYSQL_PWD');
        }

        if ($code !== 0) {
            throw new \RuntimeException('Échec de la restauration : ' . implode(' ', $sortie));
        }
    }

    /**
     * @throws \RuntimeException si aucune sauvegarde n'existe
     */
    public function restaurerDerniere(): string
    {
        $derniere = $this->derniere();
        if (!$derniere) {
            throw new \RuntimeException('Aucune sauvegarde disponible à restaurer.');
        }

        $this->restaurer($derniere);

        return $derniere;
    }

    /**
     * Valide un nom de fichier fourni par l'utilisateur (format attendu
     * uniquement, sans traversée de répertoire) et retourne son chemin
     * absolu si le fichier existe bien dans le dossier de sauvegardes.
     */
    public function cheminSecurise(string $nomFichier): ?string
    {
        if (!preg_match(self::MOTIF_NOM_FICHIER, $nomFichier)) {
            return null;
        }

        $chemin = $this->repertoire() . DIRECTORY_SEPARATOR . $nomFichier;

        return file_exists($chemin) ? $chemin : null;
    }

    private function repertoire(): string
    {
        return storage_path('app/backups');
    }

    private function assurerRepertoire(): void
    {
        if (!is_dir($this->repertoire())) {
            mkdir($this->repertoire(), 0755, true);
        }
    }

    private function purgerAnciennes(): void
    {
        $conserver = (int) config('sauvegarde.conserver', 30);
        $fichiers  = glob($this->repertoire() . DIRECTORY_SEPARATOR . '*.sql') ?: [];

        if (count($fichiers) <= $conserver) {
            return;
        }

        sort($fichiers); // ordre croissant : les plus anciens en premier
        foreach (array_slice($fichiers, 0, count($fichiers) - $conserver) as $ancien) {
            unlink($ancien);
        }
    }

    private function trouverBinaire(string $nom): ?string
    {
        $repertoires = array_filter([
            config('sauvegarde.mysql_bin_path'),
            'C:\\xampp\\mysql\\bin',
            ...(glob('C:\\wamp64\\bin\\mysql\\*\\bin') ?: []),
            ...(glob('C:\\wamp\\bin\\mysql\\*\\bin') ?: []),
        ]);

        foreach ($repertoires as $repertoire) {
            $chemin = rtrim($repertoire, '\\/') . DIRECTORY_SEPARATOR . $nom . '.exe';
            if (file_exists($chemin)) {
                return $chemin;
            }
        }

        exec('where ' . escapeshellarg($nom) . ' 2>NUL', $sortie, $code);
        if ($code === 0 && !empty($sortie)) {
            return trim($sortie[0]);
        }

        return null;
    }
}
