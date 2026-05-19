<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('garages_partenaires', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 255);
            $table->text('adresse');
            $table->string('telephone', 20);
            $table->string('ville', 255);
            $table->boolean('agree')->default(false);
            $table->timestamp('date_agrement')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('garages_partenaires');
    }
};