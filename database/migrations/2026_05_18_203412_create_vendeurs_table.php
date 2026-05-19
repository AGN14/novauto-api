<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendeurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type_compte', ['PROFESSIONNEL', 'PARTICULIER'])->default('PARTICULIER');
            $table->boolean('certifie')->default(false);
            $table->timestamp('date_certification')->nullable();
            $table->string('ifu', 20)->nullable()->unique();
            $table->string('nom_structure', 255)->nullable();
            $table->text('adresse_structure')->nullable();
            $table->string('rccm', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendeurs');
    }
};