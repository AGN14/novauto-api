<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rendez_vous', function (Blueprint $table) {
            $table->id();
            $table->foreignId('acheteur_id')->constrained('acheteurs')->onDelete('cascade');
            $table->foreignId('annonce_id')->constrained('annonces')->onDelete('cascade');
            $table->foreignId('disponibilite_id')->constrained('disponibilites')->onDelete('cascade');
            $table->timestamp('date_heure');
            $table->string('lieu', 500);
            $table->enum('statut', ['PLANIFIE', 'CONFIRME', 'ANNULE'])->default('PLANIFIE');
            $table->text('motif_annulation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rendez_vous');
    }
};