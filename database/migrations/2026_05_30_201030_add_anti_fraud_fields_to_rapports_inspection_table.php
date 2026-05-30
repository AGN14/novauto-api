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
            $table->string('code_presence', 6)->nullable()->after('heure_rdv');
            $table->timestamp('code_expire_at')->nullable()->after('code_presence');
            $table->boolean('presence_confirmee')->default(false)->after('code_expire_at');
            $table->timestamp('code_genere_at')->nullable()->after('presence_confirmee');
            $table->json('photos_inspection')->nullable()->after('observations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rapports_inspection', function (Blueprint $table) {
            $table->dropColumn([
                'code_presence',
                'code_expire_at',
                'presence_confirmee',
                'code_genere_at',
                'photos_inspection'
            ]);
        });
    }
};
