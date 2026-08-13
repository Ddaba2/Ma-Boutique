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
        // Compteurs pour les références lisibles (VENTE2026..., PROD..., CMD2026...,
        // STK...). Un compteur dédié, incrémenté sous verrou, ne recule jamais —
        // contrairement à un simple count() des lignes existantes, qui produit une
        // référence déjà attribuée dès qu'une ligne a été supprimée entre-temps.
        Schema::create('reference_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('cle')->unique();
            $table->unsignedBigInteger('valeur')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reference_sequences');
    }
};
