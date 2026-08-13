<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clotures_caisse', function (Blueprint $table) {
            // Montant réellement compté en caisse (espèces) à la clôture, saisi
            // par l'utilisateur. Sans ce champ, l'écart ne peut pas être calculé
            // par rapport à un fait réel — voir ClotureCaisseController::store().
            $table->decimal('montant_compte', 12, 2)->nullable()->after('fond_ouverture');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clotures_caisse', function (Blueprint $table) {
            $table->dropColumn('montant_compte');
        });
    }
};
