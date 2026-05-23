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
        Schema::table('avis', function (Blueprint $table) {
            $table->boolean('signale_par_vendeur')->default(false)->after('statut');
            $table->text('raison_signalement')->nullable()->after('signale_par_vendeur');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('avis', function (Blueprint $table) {
            $table->dropColumn(['signale_par_vendeur', 'raison_signalement']);
        });
    }
};
