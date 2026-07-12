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
        Schema::create('mouvements_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['entree', 'sortie', 'ajout_manuel', 'retour_client']);
            $table->integer('quantite');
            $table->decimal('prix_unitaire', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->string('reference')->unique();
            $table->text('motif')->nullable();
            $table->string('fournisseur')->nullable();
            $table->date('date_mouvement');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mouvements_stock');
    }
};
