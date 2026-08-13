<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Index composites couvrant les filtres réellement utilisés par le
     * dashboard, les rapports et la clôture de caisse (toujours scopés par
     * boutique_id via BoutiqueScope, puis filtrés par date et parfois statut).
     */
    public function up(): void
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->index(['boutique_id', 'created_at'], 'ventes_boutique_id_created_at_index');
            $table->index(['boutique_id', 'statut', 'created_at'], 'ventes_boutique_id_statut_created_at_index');
        });

        Schema::table('mouvements_stock', function (Blueprint $table) {
            $table->index(['boutique_id', 'date_mouvement'], 'mouvements_stock_boutique_id_date_mouvement_index');
            $table->index(['boutique_id', 'created_at'], 'mouvements_stock_boutique_id_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->dropIndex('ventes_boutique_id_created_at_index');
            $table->dropIndex('ventes_boutique_id_statut_created_at_index');
        });

        Schema::table('mouvements_stock', function (Blueprint $table) {
            $table->dropIndex('mouvements_stock_boutique_id_date_mouvement_index');
            $table->dropIndex('mouvements_stock_boutique_id_created_at_index');
        });
    }
};
