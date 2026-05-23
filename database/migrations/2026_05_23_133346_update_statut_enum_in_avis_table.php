<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modifier l'enum via une requête SQL brute car Laravel ne supporte pas bien le changement d'enum
        DB::statement("ALTER TABLE avis MODIFY COLUMN statut ENUM('EN_ATTENTE', 'APPROUVE', 'REJETE') DEFAULT 'EN_ATTENTE'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE avis MODIFY COLUMN statut ENUM('PUBLIE', 'SIGNALE', 'SUPPRIME') DEFAULT 'PUBLIE'");
    }
};
