<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('garages_partenaires', function (Blueprint $table) {
            $table->enum('statut_demande', ['EN_ATTENTE', 'APPROUVEE', 'REJETEE'])
                  ->default('EN_ATTENTE')
                  ->after('photo_profil');
            $table->text('message_demande')->nullable()->after('statut_demande');
        });
    }

    public function down(): void
    {
        Schema::table('garages_partenaires', function (Blueprint $table) {
            $table->dropColumn(['statut_demande', 'message_demande']);
        });
    }
};