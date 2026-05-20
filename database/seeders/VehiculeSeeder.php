<?php

namespace Database\Seeders;

use App\Models\Vehicule;
use App\Models\Modele;
use Illuminate\Database\Seeder;

class VehiculeSeeder extends Seeder
{
    public function run(): void
    {
        $vehicules = [
            ['modele' => 'Prado', 'vin' => 'JT3HP10V5V0123456', 'annee' => 2023, 'kilometrage' => 12400, 'statut_douanier' => 'DEDOUANE', 'vin_verifie' => true],
            ['modele' => 'Cayenne S', 'vin' => 'WP1ZZZ9PZAL123456', 'annee' => 2021, 'kilometrage' => 45000, 'statut_douanier' => 'DEDOUANE', 'vin_verifie' => true],
            ['modele' => 'Raptor', 'vin' => '1FTFW1ET5NFC12345', 'annee' => 2022, 'kilometrage' => 28100, 'statut_douanier' => 'EN_TRANSIT', 'vin_verifie' => true],
            ['modele' => 'GLB 250', 'vin' => 'W1N0G8DB4MF123456', 'annee' => 2022, 'kilometrage' => 31500, 'statut_douanier' => 'DEDOUANE', 'vin_verifie' => true],
            ['modele' => 'X5', 'vin' => '5UXCR6C09L9B12345', 'annee' => 2021, 'kilometrage' => 38000, 'statut_douanier' => 'EN_TRANSIT', 'vin_verifie' => true],
            ['modele' => 'Corolla Cross', 'vin' => 'JTDBRMHE0MJ012345', 'annee' => 2023, 'kilometrage' => 15000, 'statut_douanier' => 'DEDOUANE', 'vin_verifie' => false],
            ['modele' => 'RAV4', 'vin' => 'JTMRJREV0MD012345', 'annee' => 2020, 'kilometrage' => 45000, 'statut_douanier' => 'EN_TRANSIT', 'vin_verifie' => true],
            ['modele' => 'C200', 'vin' => 'WDD2050422F123456', 'annee' => 2019, 'kilometrage' => 62000, 'statut_douanier' => 'DEDOUANE', 'vin_verifie' => true],
            ['modele' => 'Civic', 'vin' => '2HGFC2F69MH123456', 'annee' => 2017, 'kilometrage' => 92000, 'statut_douanier' => 'DEDOUANE', 'vin_verifie' => false],
            ['modele' => 'Tucson', 'vin' => 'KM8J3CA40MU123456', 'annee' => 2018, 'kilometrage' => 78000, 'statut_douanier' => 'DEDOUANE', 'vin_verifie' => true],
            ['modele' => 'Qashqai', 'vin' => 'SJNFDAJ11U1234567', 'annee' => 2019, 'kilometrage' => 55000, 'statut_douanier' => 'EN_TRANSIT', 'vin_verifie' => false],
            ['modele' => 'Defender', 'vin' => 'SALCA2AX4MH123456', 'annee' => 2022, 'kilometrage' => 22000, 'statut_douanier' => 'DEDOUANE', 'vin_verifie' => true],
        ];

        foreach ($vehicules as $vehiculeData) {
            $modele = Modele::where('nom', $vehiculeData['modele'])->first();
            if ($modele) {
                Vehicule::create([
                    'modele_id'       => $modele->id,
                    'vin'             => $vehiculeData['vin'],
                    'annee'           => $vehiculeData['annee'],
                    'kilometrage'     => $vehiculeData['kilometrage'],
                    'statut_douanier' => $vehiculeData['statut_douanier'],
                    'vin_verifie'     => $vehiculeData['vin_verifie'],
                ]);
            }
        }
    }
}