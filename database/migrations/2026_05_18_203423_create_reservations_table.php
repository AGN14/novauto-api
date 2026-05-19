<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('acheteur_id')->constrained('acheteurs')->onDelete('cascade');
            $table->foreignId('annonce_id')->constrained('annonces')->onDelete('cascade');
            $table->decimal('montant_acompte', 15, 2);
            $table->timestamp('date_reservation')->useCurrent();
            $table->timestamp('date_expiration')->nullable();
            $table->enum('statut', ['EN_ATTENTE', 'CONFIRMEE', 'ANNULEE', 'EXPIREE'])->default('EN_ATTENTE');
            $table->string('document_reservation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};