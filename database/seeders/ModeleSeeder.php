<?php

namespace Database\Seeders;

use App\Models\Modele;
use App\Models\Marque;
use Illuminate\Database\Seeder;

class ModeleSeeder extends Seeder
{
    public function run(): void
    {
        $modeles = [
            // Toyota
            ['marque' => 'Toyota', 'nom' => 'Corolla', 'type_carrosserie' => 'Berline'],
            ['marque' => 'Toyota', 'nom' => 'RAV4', 'type_carrosserie' => 'SUV'],
            ['marque' => 'Toyota', 'nom' => 'Prado', 'type_carrosserie' => 'SUV'],
            ['marque' => 'Toyota', 'nom' => 'Hilux', 'type_carrosserie' => 'Pick-up'],
            ['marque' => 'Toyota', 'nom' => 'Camry', 'type_carrosserie' => 'Berline'],
            ['marque' => 'Toyota', 'nom' => 'Corolla Cross', 'type_carrosserie' => 'SUV'],

            // Mercedes
            ['marque' => 'Mercedes-Benz', 'nom' => 'C200', 'type_carrosserie' => 'Berline'],
            ['marque' => 'Mercedes-Benz', 'nom' => 'E220', 'type_carrosserie' => 'Berline'],
            ['marque' => 'Mercedes-Benz', 'nom' => 'GLC', 'type_carrosserie' => 'SUV'],
            ['marque' => 'Mercedes-Benz', 'nom' => 'GLB 250', 'type_carrosserie' => 'SUV'],

            // BMW
            ['marque' => 'BMW', 'nom' => 'X5', 'type_carrosserie' => 'SUV'],
            ['marque' => 'BMW', 'nom' => 'Serie 3', 'type_carrosserie' => 'Berline'],
            ['marque' => 'BMW', 'nom' => 'X3', 'type_carrosserie' => 'SUV'],

            // Honda
            ['marque' => 'Honda', 'nom' => 'Civic', 'type_carrosserie' => 'Berline'],
            ['marque' => 'Honda', 'nom' => 'CR-V', 'type_carrosserie' => 'SUV'],
            ['marque' => 'Honda', 'nom' => 'HR-V', 'type_carrosserie' => 'SUV'],

            // Hyundai
            ['marque' => 'Hyundai', 'nom' => 'Tucson', 'type_carrosserie' => 'SUV'],
            ['marque' => 'Hyundai', 'nom' => 'Santa Fe', 'type_carrosserie' => 'SUV'],
            ['marque' => 'Hyundai', 'nom' => 'Elantra', 'type_carrosserie' => 'Berline'],

            // Nissan
            ['marque' => 'Nissan', 'nom' => 'Qashqai', 'type_carrosserie' => 'SUV'],
            ['marque' => 'Nissan', 'nom' => 'X-Trail', 'type_carrosserie' => 'SUV'],
            ['marque' => 'Nissan', 'nom' => 'Patrol', 'type_carrosserie' => 'SUV'],

            // Ford
            ['marque' => 'Ford', 'nom' => 'Raptor', 'type_carrosserie' => 'Pick-up'],
            ['marque' => 'Ford', 'nom' => 'Explorer', 'type_carrosserie' => 'SUV'],

            // Porsche
            ['marque' => 'Porsche', 'nom' => 'Cayenne S', 'type_carrosserie' => 'SUV'],
            ['marque' => 'Porsche', 'nom' => 'Macan', 'type_carrosserie' => 'SUV'],

            // Land Rover
            ['marque' => 'Land Rover', 'nom' => 'Defender', 'type_carrosserie' => 'SUV'],
            ['marque' => 'Land Rover', 'nom' => 'Discovery', 'type_carrosserie' => 'SUV'],

            // Lexus
            ['marque' => 'Lexus', 'nom' => 'LX570', 'type_carrosserie' => 'SUV'],
            ['marque' => 'Lexus', 'nom' => 'RX350', 'type_carrosserie' => 'SUV'],
        ];

        foreach ($modeles as $modeleData) {
            $marque = Marque::where('nom', $modeleData['marque'])->first();
            if ($marque) {
                Modele::create([
                    'marque_id'       => $marque->id,
                    'nom'             => $modeleData['nom'],
                    'type_carrosserie'=> $modeleData['type_carrosserie'],
                ]);
            }
        }
    }
}