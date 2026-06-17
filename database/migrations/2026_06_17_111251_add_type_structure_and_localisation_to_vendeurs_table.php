<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendeurs', function (Blueprint $table) {
            $table->enum('type_structure', ['PARC_AUTO', 'CONCESSIONNAIRE'])
                  ->nullable()
                  ->after('type_compte');
            $table->decimal('latitude', 10, 7)->nullable()->after('adresse_structure');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('vendeurs', function (Blueprint $table) {
            $table->dropColumn(['type_structure', 'latitude', 'longitude']);
        });
    }
};