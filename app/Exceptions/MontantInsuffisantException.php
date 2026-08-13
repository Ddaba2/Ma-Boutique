<?php

namespace App\Exceptions;

class MontantInsuffisantException extends \RuntimeException
{
    public function __construct(public readonly float $montantRecu, public readonly float $total)
    {
        parent::__construct("Le montant reçu ({$montantRecu} FCFA) est inférieur au total de la vente ({$total} FCFA).");
    }
}
