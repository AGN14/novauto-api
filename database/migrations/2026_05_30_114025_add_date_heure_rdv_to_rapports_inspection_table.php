<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rapports_inspection', function (Blueprint $table) {
            $table->date('date_rdv')->nullable()->after('garage_id');
            $table->time('heure_rdv')->nullable()->after('date_rdv');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rapports_inspection', function (Blueprint $table) {
            $table->dropColumn(['date_rdv', 'heure_rdv']);
        });
    }
};
