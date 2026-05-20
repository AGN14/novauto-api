<?php

namespace App\Services;

use App\Models\Marque;
use App\Models\Modele;
use App\Models\Vehicule;
use Illuminate\Support\Facades\Http;

class VinService
{
    private const NHTSA_URL = 'https://vpic.nhtsa.dot.gov/api/vehicles/decodevin';

    public function decode(string $vin): array
    {
        $vin = strtoupper(trim($vin));

        if (strlen($vin) !== 17) {
            throw new \Exception('Le numéro VIN doit contenir exactement 17 caractères.');
        }

        $response = Http::timeout(15)
            ->withoutVerifying()
            ->get(self::NHTSA_URL . "/{$vin}?format=json");

        if (!$response->successful()) {
            throw new \Exception('Impossible de contacter l\'API NHTSA. Réessayez plus tard.');
        }

        $results = $response->json('Results', []);

        $getValue = function (string $variable) use ($results) {
            $item = collect($results)->firstWhere('Variable', $variable);
            $value = $item['Value'] ?? null;
            if (!$value || in_array($value, ['Not Applicable', 'null', '0'])) {
                return null;
            }
            return $value;
        };

        $marqueNom  = $getValue('Make');
        $modeleNom  = $getValue('Model');
        $annee      = $getValue('Model Year');

        if (!$marqueNom) {
            throw new \Exception('VIN invalide ou non reconnu dans la base NHTSA.');
        }

        $marque = Marque::firstOrCreate(
            ['nom' => $marqueNom],
            ['pays_origine' => $getValue('Plant Country')]
        );

        $modele = null;
        if ($modeleNom) {
            $modele = Modele::firstOrCreate(
                ['marque_id' => $marque->id, 'nom' => $modeleNom],
                ['type_carrosserie' => $getValue('Body Class')]
            );
        }

        $vehicule = Vehicule::where('vin', $vin)->first();
        if ($vehicule) {
            $vehicule->update(['vin_verifie' => true]);
            if ($modele) {
                $vehicule->update(['modele_id' => $modele->id]);
            }
        }

        return [
            'vin'             => $vin,
            'wmi'             => substr($vin, 0, 3),
            'vds'             => substr($vin, 3, 6),
            'vis'             => substr($vin, 9, 8),
            'marque'          => $marqueNom,
            'modele'          => $modeleNom,
            'annee'           => $annee,
            'fabricant'       => $getValue('Manufacturer Name'),
            'type'            => $getValue('Vehicle Type'),
            'carrosserie'     => $getValue('Body Class'),
            'serie'           => $getValue('Series'),
            'cylindres'       => $getValue('Engine Number of Cylinders'),
            'cylindree'       => $getValue('Displacement (CC)'),
            'cylindreeLitres' => $getValue('Displacement (L)'),
            'carburant'       => $getValue('Fuel Type - Primary'),
            'transmission'    => $getValue('Transmission Style'),
            'vitesses'        => $getValue('Transmission Speeds'),
            'traction'        => $getValue('Drive Type'),
            'puissance'       => $getValue('Engine Brake (hp) From'),
            'portes'          => $getValue('Doors'),
            'places'          => $getValue('Seats'),
            'turbo'           => $getValue('Turbo'),
            'freinageABS'     => $getValue('Anti-Lock Braking System (ABS)'),
            'pays'            => $getValue('Plant Country'),
            'ville'           => $getValue('Plant City'),
            'vin_verifie'     => true,
            'vehicule_trouve' => $vehicule !== null,
            'marque_creee'    => $marque->wasRecentlyCreated,
            'modele_cree'     => $modele?->wasRecentlyCreated ?? false,
        ];
    }
}