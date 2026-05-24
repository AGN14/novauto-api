<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('rendez_vous');

        Schema::create('rendez_vous', function (Blueprint $table) {
            $table->id();
            $table->foreignId('acheteur_id')->constrained('acheteurs')->onDelete('cascade');
            $table->foreignId('annonce_id')->constrained('annonces')->onDelete('cascade');
            $table->date('date_rdv');
            $table->time('heure_rdv');
            $table->text('message')->nullable();
            $table->text('message_vendeur')->nullable();
            $table->enum('statut', ['EN_ATTENTE', 'CONFIRME', 'ANNULE', 'AUTRE_DATE_PROPOSEE'])->default('EN_ATTENTE');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rendez_vous');
    }
};
