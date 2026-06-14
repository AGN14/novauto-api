<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('garages_partenaires', function (Blueprint $table) {
            $table->decimal('prix_inspection', 10, 2)->default(2000)->change();
        });

        // Mettre à jour les garages existants qui ont encore 3000
        DB::table('garages_partenaires')
            ->where('prix_inspection', 3000)
            ->update(['prix_inspection' => 2000]);
    }

    public function down(): void
    {
        Schema::table('garages_partenaires', function (Blueprint $table) {
            $table->decimal('prix_inspection', 10, 2)->default(3000)->change();
        });
    }
};