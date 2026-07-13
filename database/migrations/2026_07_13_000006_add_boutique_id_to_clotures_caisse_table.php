<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clotures_caisse', function (Blueprint $table) {
            $table->dropUnique(['date']);
            $table->foreignId('boutique_id')->nullable()->after('id')->constrained()->restrictOnDelete();
            $table->unique(['date', 'boutique_id']);
        });
    }

    public function down(): void
    {
        Schema::table('clotures_caisse', function (Blueprint $table) {
            $table->dropUnique(['date', 'boutique_id']);
            $table->dropForeign(['boutique_id']);
            $table->dropColumn('boutique_id');
            $table->unique('date');
        });
    }
};
