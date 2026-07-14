<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Champs nécessaires à la synchronisation des ventes créées hors-ligne.
     *
     * uuid_client : identifiant généré côté navigateur au moment de la mise
     * en file d'attente, permet à /api/ventes/sync de détecter un rejeu
     * (retry réseau) sans dupliquer la vente.
     *
     * conflit_stock / notes_conflit : plutôt que d'ajouter une valeur
     * 'conflit' à l'enum `statut` (ALTER ENUM fragile et différent entre
     * MySQL et SQLite sans doctrine/dbal), un simple indicateur booléen
     * marque une vente synchronisée malgré un stock insuffisant au moment
     * du replay, à régulariser manuellement par le gérant.
     */
    public function up(): void
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->string('uuid_client')->nullable()->unique()->after('reference');
            $table->boolean('conflit_stock')->default(false)->after('statut');
            $table->text('notes_conflit')->nullable()->after('conflit_stock');
        });
    }

    public function down(): void
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->dropColumn(['uuid_client', 'conflit_stock', 'notes_conflit']);
        });
    }
};
