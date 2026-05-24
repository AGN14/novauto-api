<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Modifier l'enum pour ajouter PAIEMENT
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM(
            'RENDEZ_VOUS', 'RESERVATION', 'AVIS', 'ANNONCE',
            'RESERVATION_CONFIRMEE', 'PAIEMENT_REUSSI', 'PAIEMENT_ECHOUE', 'PAIEMENT',
            'RDV_PLANIFIE', 'RDV_CONFIRME', 'RDV_ANNULE', 'RDV_RAPPEL',
            'RESERVATION_EXPIREE', 'VENDEUR_CERTIFIE', 'RAPPORT_SOUMIS',
            'RAPPORT_VALIDE', 'AVIS_PUBLIE', 'AVIS_SUPPRIME', 'COMPTE_VENDEUR_CREE'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM(
            'RENDEZ_VOUS', 'RESERVATION', 'AVIS', 'ANNONCE',
            'RESERVATION_CONFIRMEE', 'PAIEMENT_REUSSI', 'PAIEMENT_ECHOUE',
            'RDV_PLANIFIE', 'RDV_CONFIRME', 'RDV_ANNULE', 'RDV_RAPPEL',
            'RESERVATION_EXPIREE', 'VENDEUR_CERTIFIE', 'RAPPORT_SOUMIS',
            'RAPPORT_VALIDE', 'AVIS_PUBLIE', 'AVIS_SUPPRIME', 'COMPTE_VENDEUR_CREE'
        ) NOT NULL");
    }
};
