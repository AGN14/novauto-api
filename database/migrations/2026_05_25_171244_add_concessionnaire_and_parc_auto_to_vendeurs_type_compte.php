<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE vendeurs MODIFY type_compte ENUM('PARTICULIER', 'PROFESSIONNEL', 'CONCESSIONNAIRE', 'PARC_AUTO') DEFAULT 'PARTICULIER'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE vendeurs MODIFY type_compte ENUM('PARTICULIER', 'PROFESSIONNEL') DEFAULT 'PARTICULIER'");
    }
};
