<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modeles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marque_id')->constrained('marques')->onDelete('cascade');
            $table->string('nom', 255);
            $table->string('type_carrosserie', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modeles');
    }
};