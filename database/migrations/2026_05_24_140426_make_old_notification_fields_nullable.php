<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->enum('canal', ['EMAIL', 'SMS'])->nullable()->change();
            $table->text('contenu')->nullable()->change();
            $table->enum('statut_envoi', ['EN_ATTENTE', 'ENVOYE', 'ECHOUE'])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->enum('canal', ['EMAIL', 'SMS'])->nullable(false)->change();
            $table->text('contenu')->nullable(false)->change();
            $table->enum('statut_envoi', ['EN_ATTENTE', 'ENVOYE', 'ECHOUE'])->default('EN_ATTENTE')->change();
        });
    }
};
