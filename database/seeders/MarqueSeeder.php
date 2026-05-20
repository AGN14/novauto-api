<?php

namespace Database\Seeders;

use App\Models\Marque;
use Illuminate\Database\Seeder;

class MarqueSeeder extends Seeder
{
    public function run(): void
    {
        $marques = [
            ['nom' => 'Toyota', 'pays_origine' => 'Japon'],
            ['nom' => 'Mercedes-Benz', 'pays_origine' => 'Allemagne'],
            ['nom' => 'BMW', 'pays_origine' => 'Allemagne'],
            ['nom' => 'Honda', 'pays_origine' => 'Japon'],
            ['nom' => 'Hyundai', 'pays_origine' => 'Corée du Sud'],
            ['nom' => 'Nissan', 'pays_origine' => 'Japon'],
            ['nom' => 'Ford', 'pays_origine' => 'États-Unis'],
            ['nom' => 'Volkswagen', 'pays_origine' => 'Allemagne'],
            ['nom' => 'Porsche', 'pays_origine' => 'Allemagne'],
            ['nom' => 'Kia', 'pays_origine' => 'Corée du Sud'],
            ['nom' => 'Peugeot', 'pays_origine' => 'France'],
            ['nom' => 'Renault', 'pays_origine' => 'France'],
            ['nom' => 'Mitsubishi', 'pays_origine' => 'Japon'],
            ['nom' => 'Land Rover', 'pays_origine' => 'Royaume-Uni'],
            ['nom' => 'Lexus', 'pays_origine' => 'Japon'],
        ];

        foreach ($marques as $marque) {
            Marque::create($marque);
        }
    }
}