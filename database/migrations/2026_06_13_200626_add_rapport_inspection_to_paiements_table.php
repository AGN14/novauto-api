<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            // Rendre reservation_id nullable
            $table->foreignId('reservation_id')->nullable()->change();

            // Ajouter rapport_inspection_id
            $table->foreignId('rapport_inspection_id')
                ->nullable()
                ->after('reservation_id')
                ->constrained('rapports_inspection')
                ->onDelete('cascade');

            // Ajouter le type de paiement
            $table->enum('type', ['RESERVATION', 'INSPECTION'])->default('RESERVATION')->after('rapport_inspection_id');
        });
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropForeign(['rapport_inspection_id']);
            $table->dropColumn(['rapport_inspection_id', 'type']);
            $table->foreignId('reservation_id')->nullable(false)->change();
        });
    }
};