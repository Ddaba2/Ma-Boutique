<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boutique_produit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boutique_id')->constrained()->cascadeOnDelete();
            $table->foreignId('produit_id')->constrained()->cascadeOnDelete();
            $table->integer('stock_actuel')->default(0);
            $table->integer('stock_min')->default(5);
            $table->integer('stock_max')->default(100);
            $table->timestamps();

            $table->unique(['boutique_id', 'produit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boutique_produit');
    }
};
