<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MarqueSeeder::class,
            ModeleSeeder::class,
            VehiculeSeeder::class,
            AnnonceSeeder::class,
        ]);
    }
}