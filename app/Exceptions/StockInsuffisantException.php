<?php

namespace App\Exceptions;

class StockInsuffisantException extends \RuntimeException
{
    public function __construct(public readonly string $produitNom, public readonly int $stockDisponible)
    {
        parent::__construct("Stock insuffisant pour le produit : {$produitNom}. Stock disponible: {$stockDisponible}");
    }
}
