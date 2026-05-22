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
            $table->timestamp('date_inspection')->nullable();
            $table->text('etat_carrosserie')->nullable();
            $table->text('etat_moteur')->nullable();
            $table->text('etat_freins')->nullable();
            $table->text('etat_pneus')->nullable();
            $table->integer('kilometrage_verifie')->nullable();
            $table->text('observations')->nullable();
            $table->enum('statut', ['EN_ATTENTE', 'VALIDEE', 'REJETEE'])->default('EN_ATTENTE');
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