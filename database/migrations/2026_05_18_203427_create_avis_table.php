<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('acheteur_id')->constrained('acheteurs')->onDelete('cascade');
            $table->foreignId('vendeur_id')->constrained('vendeurs')->onDelete('cascade');
            $table->foreignId('annonce_id')->constrained('annonces')->onDelete('cascade');
            $table->enum('type', ['AVIS_VENDEUR', 'AVIS_VEHICULE']);
            $table->integer('note')->unsigned();
            $table->text('commentaire');
            $table->boolean('signale')->default(false);
            $table->enum('statut', ['PUBLIE', 'SIGNALE', 'SUPPRIME'])->default('PUBLIE');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avis');
    }
};