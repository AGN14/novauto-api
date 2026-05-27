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
        Schema::table('garages_partenaires', function (Blueprint $table) {
            $table->string('email')->unique()->nullable()->after('ville');
            $table->string('password')->nullable()->after('email');
            $table->boolean('certifie')->default(false)->after('agree');
            $table->timestamp('date_certification')->nullable()->after('certifie');
            $table->decimal('prix_inspection', 10, 2)->default(3000)->after('date_certification');
            $table->string('photo_profil')->nullable()->after('prix_inspection');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('garages_partenaires', function (Blueprint $table) {
            $table->dropColumn([
                'email',
                'password',
                'certifie',
                'date_certification',
                'prix_inspection',
                'photo_profil'
            ]);
        });
    }
};
