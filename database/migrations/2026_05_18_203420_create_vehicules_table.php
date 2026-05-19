<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modele_id')->constrained('modeles')->onDelete('cascade');
            $table->string('vin', 17)->unique();
            $table->integer('annee');
            $table->integer('kilometrage');
            $table->enum('statut_douanier', ['DEDOUANE', 'EN_TRANSIT'])->default('DEDOUANE');
            $table->boolean('vin_verifie')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicules');
    }
};