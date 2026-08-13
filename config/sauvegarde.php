<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dossier des binaires MySQL (mysqldump.exe / mysql.exe)
    |--------------------------------------------------------------------------
    | Laisser vide pour une détection automatique (XAMPP, WAMP, PATH système).
    | À renseigner uniquement si l'installation MySQL est ailleurs, ex:
    | SAUVEGARDE_MYSQL_BIN_PATH="C:\xampp\mysql\bin"
    */
    'mysql_bin_path' => env('SAUVEGARDE_MYSQL_BIN_PATH'),

    /*
    |--------------------------------------------------------------------------
    | Rétention
    |--------------------------------------------------------------------------
    | Nombre de fichiers de sauvegarde conservés dans storage/app/backups
    | (sauvegardes manuelles + sauvegardes de sécurité avant restauration
    | confondues). Les plus anciens sont supprimés au-delà de cette limite.
    */
    'conserver' => env('SAUVEGARDE_CONSERVER', 30),

];
