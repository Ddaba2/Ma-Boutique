<?php

namespace App\Support;

/**
 * Règles de validation réutilisables (sécurité XSS côté stockage).
 */
class ReglesChamps
{
    /** Refuse les chevrons HTML dans les libellés affichés en interface. */
    public const NOM_SANS_HTML = 'regex:/^[^<>]*$/';
}
