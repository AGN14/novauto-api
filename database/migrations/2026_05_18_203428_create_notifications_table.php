<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destinataire_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', [
                'RESERVATION_CONFIRMEE', 'PAIEMENT_REUSSI', 'PAIEMENT_ECHOUE',
                'RDV_PLANIFIE', 'RDV_CONFIRME', 'RDV_ANNULE', 'RDV_RAPPEL',
                'RESERVATION_EXPIREE', 'VENDEUR_CERTIFIE', 'RAPPORT_SOUMIS',
                'RAPPORT_VALIDE', 'AVIS_PUBLIE', 'AVIS_SUPPRIME', 'COMPTE_VENDEUR_CREE'
            ]);
            $table->enum('canal', ['EMAIL', 'SMS']);
            $table->string('sujet', 255)->nullable();
            $table->text('contenu');
            $table->enum('statut_envoi', ['EN_ATTENTE', 'ENVOYE', 'ECHOUE'])->default('EN_ATTENTE');
            $table->integer('nombre_tentatives')->default(0);
            $table->timestamp('date_envoi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};