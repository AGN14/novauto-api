<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('annonces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendeur_id')->constrained('vendeurs')->onDelete('cascade');
            $table->foreignId('vehicule_id')->constrained('vehicules')->onDelete('cascade');
            $table->string('titre', 255);
            $table->decimal('prix', 15, 2);
            $table->enum('statut', ['DISPONIBLE', 'RESERVEE', 'VENDUE', 'EXPIREE'])->default('DISPONIBLE');
            $table->json('photos')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annonces');
    }
};