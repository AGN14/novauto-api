<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->onDelete('cascade');
            $table->foreignId('acheteur_id')->constrained('acheteurs')->onDelete('cascade');
            $table->decimal('montant', 15, 2);
            $table->enum('moyen', ['MOBILE_MONEY', 'CARTE_BANCAIRE']);
            $table->enum('statut', ['EN_ATTENTE', 'SUCCES', 'ECHEC'])->default('EN_ATTENTE');
            $table->string('reference_externe')->nullable();
            $table->timestamp('date_transaction')->useCurrent();
            $table->string('recu')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};