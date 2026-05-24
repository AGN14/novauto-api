<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            VendeurSeeder::class,
            AcheteurSeeder::class,
            MarqueSeeder::class,
            ModeleSeeder::class,
            VehiculeSeeder::class,
            AnnonceSeeder::class,
            AvisSeeder::class,
        ]);
    }
}