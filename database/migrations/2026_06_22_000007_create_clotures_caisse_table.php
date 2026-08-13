<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clotures_caisse', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->decimal('fond_ouverture', 12, 2)->default(0);
            $table->decimal('total_especes', 12, 2)->default(0);
            $table->decimal('total_carte', 12, 2)->default(0);
            $table->decimal('total_mobile', 12, 2)->default(0);
            $table->decimal('total_autre', 12, 2)->default(0);
            $table->decimal('total_ventes', 12, 2)->default(0);
            $table->integer('nombre_ventes')->default(0);
            $table->decimal('ecart', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->enum('statut', ['ouvert', 'clos'])->default('ouvert');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clotures_caisse');
    }
};
