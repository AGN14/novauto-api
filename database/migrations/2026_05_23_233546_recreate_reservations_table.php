<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('paiements');
        Schema::dropIfExists('reservations');
        Schema::enableForeignKeyConstraints();

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('acheteur_id')->constrained('acheteurs')->onDelete('cascade');
            $table->foreignId('annonce_id')->constrained('annonces')->onDelete('cascade');
            $table->decimal('montant_paye', 15, 2);
            $table->enum('statut', ['EN_ATTENTE', 'CONFIRMEE', 'ANNULEE'])->default('EN_ATTENTE');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
