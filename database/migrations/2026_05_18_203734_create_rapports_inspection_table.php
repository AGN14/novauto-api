<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rapports_inspection', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicule_id')->constrained('vehicules')->onDelete('cascade');
            $table->foreignId('garage_id')->constrained('garages_partenaires')->onDelete('cascade');
            $table->timestamp('date_inspection');
            $table->text('etat_carrosserie');
            $table->text('etat_moteur');
            $table->text('etat_freins');
            $table->text('etat_pneus');
            $table->integer('kilometrage_verifie');
            $table->text('observations')->nullable();
            $table->enum('statut', ['EN_ATTENTE', 'APPROUVE', 'REJETE'])->default('EN_ATTENTE');
            $table->timestamp('date_soumission')->useCurrent();
            $table->timestamp('date_validation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rapports_inspection');
    }
};