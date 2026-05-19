<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disponibilites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendeur_id')->constrained('vendeurs')->onDelete('cascade');
            $table->date('jour');
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->enum('statut', ['LIBRE', 'OCCUPE'])->default('LIBRE');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disponibilites');
    }
};