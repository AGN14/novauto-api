<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Ajouter les nouvelles colonnes
            $table->string('titre')->after('destinataire_id');
            $table->text('message')->after('titre');
            $table->string('lien')->nullable()->after('message');
            $table->boolean('lu')->default(false)->after('lien');

            // Modifier la colonne type pour ajouter les nouveaux types
            $table->dropColumn('type');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->enum('type', [
                'RENDEZ_VOUS', 'RESERVATION', 'AVIS', 'ANNONCE',
                'RESERVATION_CONFIRMEE', 'PAIEMENT_REUSSI', 'PAIEMENT_ECHOUE',
                'RDV_PLANIFIE', 'RDV_CONFIRME', 'RDV_ANNULE', 'RDV_RAPPEL',
                'RESERVATION_EXPIREE', 'VENDEUR_CERTIFIE', 'RAPPORT_SOUMIS',
                'RAPPORT_VALIDE', 'AVIS_PUBLIE', 'AVIS_SUPPRIME', 'COMPTE_VENDEUR_CREE'
            ])->after('destinataire_id');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['titre', 'message', 'lien', 'lu']);
        });
    }
};
