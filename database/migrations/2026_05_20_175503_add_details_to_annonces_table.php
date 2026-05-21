<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('annonces', function (Blueprint $table) {
            $table->text('description')->nullable()->after('photos');
            $table->json('equipements')->nullable()->after('description');
            $table->string('ville')->default('Cotonou')->after('equipements');
        });
    }

    public function down(): void
    {
        Schema::table('annonces', function (Blueprint $table) {
            $table->dropColumn(['description', 'equipements', 'ville']);
        });
    }
};