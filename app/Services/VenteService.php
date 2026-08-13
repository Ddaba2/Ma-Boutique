<?php

namespace App\Services;

use App\Exceptions\MontantInsuffisantException;
use App\Exceptions\StockInsuffisantException;
use App\Models\BoutiqueProduit;
use App\Models\DetailVente;
use App\Models\MouvementStock;
use App\Models\Produit;
use App\Models\Vente;
use Illuminate\Support\Facades\DB;

/**
 * Logique d'enregistrement d'une vente, partagée entre le flux de caisse
 * synchrone (VenteController::store) et la synchronisation des ventes créées
 * hors-ligne (VenteSyncController::sync).
 */
class VenteService
{
    /**
     * @param array{
     *   client_nom: string,
     *   client_telephone: ?string,
     *   mode_paiement: string,
     *   montant_recu: float,
     *   uuid_client?: ?string,
     *   lignes: array<int, array{produit_id: int, quantite: int, prix_unitaire: float}>,
     * } $data
     * @param bool $tolererConflitStock Si true, la vente est créée même si le
     *   stock est insuffisant ou si le montant reçu ne couvre pas le total
     *   (cas d'une synchronisation hors-ligne tardive : la vente a déjà eu
     *   lieu physiquement, la rejeter ferait perdre la donnée), et marquée
     *   conflit_stock=true pour résolution manuelle plutôt que d'être rejetée.
     *   En flux caisse synchrone, ces deux anomalies sont bloquantes.
     */
    public function creerVente(array $data, int $boutiqueId, bool $tolererConflitStock = false): Vente
    {
        return DB::transaction(function () use ($data, $boutiqueId, $tolererConflitStock) {
            $total = 0;
            $items = [];
            $conflit = false;
            $notesConflit = [];

            // Verrouiller les lignes de stock dans un ordre stable (par
            // produit_id) : deux ventes concurrentes contenant les mêmes
            // produits mais saisis dans un ordre différent ne peuvent alors
            // jamais se verrouiller en sens inverse et provoquer un deadlock.
            $lignes = collect($data['lignes'])->sortBy('produit_id')->values()->all();

            foreach ($lignes as $ligne) {
                $produit = Produit::findOrFail($ligne['produit_id']);
                $qty = (int) $ligne['quantite'];
                $prixUnitaire = (float) $ligne['prix_unitaire'];

                $stockBoutique = BoutiqueProduit::dansBoutique($boutiqueId)
                    ->where('produit_id', $produit->id)
                    ->lockForUpdate()
                    ->first();
                $stockDisponible = $stockBoutique->stock_actuel ?? 0;

                if ($stockDisponible < $qty) {
                    if (!$tolererConflitStock) {
                        throw new StockInsuffisantException($produit->nom, $stockDisponible);
                    }
                    $conflit = true;
                    $notesConflit[] = "{$produit->nom} : demandé {$qty}, disponible {$stockDisponible}";
                }

                $ligneTotal = $qty * $prixUnitaire;
                $total += $ligneTotal;

                $items[] = [
                    'produit' => $produit,
                    'stock_boutique' => $stockBoutique,
                    'quantite' => $qty,
                    'prix_unitaire' => $prixUnitaire,
                    'total_ligne' => $ligneTotal,
                ];
            }

            $montantRecu = (float) $data['montant_recu'];

            if ($montantRecu < $total) {
                if (!$tolererConflitStock) {
                    throw new MontantInsuffisantException($montantRecu, $total);
                }
                $conflit = true;
                $notesConflit[] = "Montant reçu ({$montantRecu}) inférieur au total ({$total})";
            }

            $reference = Vente::generateReference();

            $vente = Vente::create([
                'reference' => $reference,
                'uuid_client' => $data['uuid_client'] ?? null,
                'boutique_id' => $boutiqueId,
                'total' => $total,
                'montant_recu' => $montantRecu,
                'monnaie' => $montantRecu - $total,
                'client_nom' => $data['client_nom'],
                'client_telephone' => $data['client_telephone'] ?? null,
                'mode_paiement' => $data['mode_paiement'],
                'statut' => 'terminee',
                'conflit_stock' => $conflit,
                'notes_conflit' => $conflit ? implode(' | ', $notesConflit) : null,
            ]);

            foreach ($items as $item) {
                $produit = $item['produit'];
                $qty = $item['quantite'];
                $stockBoutique = $item['stock_boutique'];

                DetailVente::create([
                    'vente_id' => $vente->id,
                    'produit_id' => $produit->id,
                    'quantite' => $qty,
                    'prix_unitaire' => $item['prix_unitaire'],
                    'total_ligne' => $item['total_ligne'],
                ]);

                if ($stockBoutique) {
                    // Ne jamais descendre sous 0 : en cas de conflit toléré, on vide
                    // le stock disponible plutôt que de produire un nombre négatif.
                    $decrement = min($qty, $stockBoutique->stock_actuel);
                    if ($decrement > 0) {
                        $stockBoutique->decrement('stock_actuel', $decrement);
                    }
                }

                MouvementStock::create([
                    'boutique_id' => $boutiqueId,
                    'produit_id' => $produit->id,
                    'type' => 'sortie',
                    'quantite' => $qty,
                    'prix_unitaire' => $item['prix_unitaire'],
                    'total' => $item['total_ligne'],
                    'reference' => MouvementStock::generateReference(),
                    'motif' => 'Vente facture ' . $reference,
                    'date_mouvement' => now(),
                    'notes' => 'Client: ' . $data['client_nom'],
                ]);
            }

            return $vente;
        });
    }
}
