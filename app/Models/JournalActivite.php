<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBoutique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Piste d'audit pour les actions sensibles qui ne sont pas déjà tracées ailleurs
 * (contrairement aux mouvements de stock, qui ont leur propre historique dans
 * MouvementStock) : changement de prix, annulation de vente, ajustement de
 * stock manuel. Lecture seule depuis l'application, alimentée uniquement via
 * enregistrer().
 */
class JournalActivite extends Model
{
    use BelongsToBoutique;

    protected $fillable = [
        'user_id',
        'boutique_id',
        'action',
        'sujet_type',
        'sujet_id',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sujet()
    {
        return $this->morphTo();
    }

    public static function libellesAction(): array
    {
        return [
            'produit.prix_modifie' => ['Prix modifié', 'warning'],
            'vente.annulee' => ['Vente annulée', 'danger'],
            'stock.ajuste' => ['Stock ajusté', 'info'],
        ];
    }

    public function libelleAction(): string
    {
        return self::libellesAction()[$this->action][0] ?? $this->action;
    }

    public function badgeAction(): string
    {
        return self::libellesAction()[$this->action][1] ?? 'secondary';
    }

    public static function enregistrer(string $action, string $description, ?Model $sujet = null): self
    {
        // boutique_id est renseigné automatiquement par BelongsToBoutique.
        return static::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'sujet_type' => $sujet ? $sujet::class : null,
            'sujet_id' => $sujet?->getKey(),
            'description' => $description,
        ]);
    }
}
