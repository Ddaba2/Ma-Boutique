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
        Schema::create('ventes', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->decimal('total', 10, 2);
            $table->decimal('montant_recu', 10, 2);
            $table->decimal('monnaie', 10, 2);
            $table->string('client_nom')->nullable();
            $table->string('client_telephone')->nullable();
            $table->string('client_email')->nullable();
            $table->text('notes')->nullable();
            $table->enum('statut', ['en_cours', 'terminee', 'annulee'])->default('terminee');
            $table->enum('mode_paiement', ['espece', 'carte', 'mobile', 'autre'])->default('espece');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventes');
    }
};
