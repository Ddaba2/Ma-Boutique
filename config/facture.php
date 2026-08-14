<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Logo de l'entreprise / client
    |--------------------------------------------------------------------------
    | Placez le logo client dans public/ (PNG, JPG ou SVG) et indiquez le
    | chemin ici, ex: 'logo-client.png'. Utilisé dans la sidebar et les factures.
    | Priorité : logo en base (entreprises) > ce chemin > logo-client.svg.
    */
    'logo_path' => 'logo-client.svg',

    /*
    |--------------------------------------------------------------------------
    | Informations émetteur (valeurs par défaut)
    |--------------------------------------------------------------------------
    | Utilisées si aucune fiche Entreprise n'est enregistrée en base.
    | Modifiez ces valeurs pour personnaliser vos factures.
    */
    'entreprise' => [
        'nom' => 'GesBoutique',
        'slogan' => 'Matériel informatique & mobilier de bureau',
        'nif' => 'NIF — — — — —',
        'adresse' => "Adresse de l'entreprise\nVille, Pays",
        'telephone' => '+223 — — — — —',
        'email' => 'contact@gesboutique.com',
        'site_web' => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | Adresse client par défaut
    |--------------------------------------------------------------------------
    | Affichée lorsqu'aucune adresse n'est connue pour le client.
    */
    'client_adresse_defaut' => "Adresse du client\nVille, Pays",

    'conditions_paiement' => 'Paiement comptant à la livraison. Merci de conserver ce document.',
    'mention_legale' => 'Document généré automatiquement — valable sans signature pour les ventes au comptant.',

];
