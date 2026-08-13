<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mouvements_stock', function (Blueprint $table) {
            $table->foreignId('boutique_id')->nullable()->after('id')->constrained()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mouvements_stock', function (Blueprint $table) {
            $table->dropForeign(['boutique_id']);
            $table->dropColumn('boutique_id');
        });
    }
};
