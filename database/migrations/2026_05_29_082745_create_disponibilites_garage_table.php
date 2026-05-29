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
        Schema::create('disponibilites_garage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('garage_id')
                  ->constrained('garages_partenaires')
                  ->onDelete('cascade');
            $table->date('jour');
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->enum('statut', ['LIBRE', 'OCCUPE'])->default('LIBRE');
            $table->timestamps();

            // Index pour améliorer les performances
            $table->index(['garage_id', 'jour', 'statut']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disponibilites_garage');
    }
};
