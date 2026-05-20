<?php

namespace Database\Seeders;

use App\Models\Annonce;
use App\Models\Vehicule;
use App\Models\Vendeur;
use Illuminate\Database\Seeder;

class AnnonceSeeder extends Seeder
{
    public function run(): void
    {
        $vendeur = Vendeur::first();

        if (!$vendeur) {
            $this->command->info('Aucun vendeur trouvé. Créez d\'abord un vendeur.');
            return;
        }

        $annonces = [
            ['vehicule_vin' => 'JT3HP10V5V0123456', 'titre' => 'Toyota Prado 2023 - Excellent état', 'prix' => 45500000, 'photos' => ['https://images.unsplash.com/photo-1594502184342-2e12f877aa73?w=800&q=80']],
            ['vehicule_vin' => 'WP1ZZZ9PZAL123456', 'titre' => 'Porsche Cayenne S 2021 - Luxe absolu', 'prix' => 32000000, 'photos' => ['https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&q=80']],
            ['vehicule_vin' => '1FTFW1ET5NFC12345', 'titre' => 'Ford Raptor 2022 - Puissance et style', 'prix' => 58200000, 'photos' => ['https://images.unsplash.com/photo-1612825173281-9a193378527e?w=800&q=80']],
            ['vehicule_vin' => 'W1N0G8DB4MF123456', 'titre' => 'Mercedes GLB 250 2022 - Premium', 'prix' => 27800000, 'photos' => ['https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?w=800&q=80']],
            ['vehicule_vin' => '5UXCR6C09L9B12345', 'titre' => 'BMW X5 xDrive 2021 - Sport luxe', 'prix' => 42000000, 'photos' => ['https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&q=80']],
            ['vehicule_vin' => 'JTDBRMHE0MJ012345', 'titre' => 'Toyota Corolla Cross 2023 - Neuf', 'prix' => 19500000, 'photos' => ['https://images.unsplash.com/photo-1623869675781-80aa31012a5a?w=800&q=80']],
            ['vehicule_vin' => 'JTMRJREV0MD012345', 'titre' => 'Toyota RAV4 2020 - Familial', 'prix' => 18500000, 'photos' => ['https://images.unsplash.com/photo-1581540222194-0def2dda95b8?w=800&q=80']],
            ['vehicule_vin' => 'WDD2050422F123456', 'titre' => 'Mercedes C200 2019 - Élégance', 'prix' => 15200000, 'photos' => ['https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?w=800&q=80']],
            ['vehicule_vin' => '2HGFC2F69MH123456', 'titre' => 'Honda Civic 2017 - Économique', 'prix' => 7800000, 'photos' => ['https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?w=800&q=80']],
            ['vehicule_vin' => 'KM8J3CA40MU123456', 'titre' => 'Hyundai Tucson 2018 - Confort', 'prix' => 11200000, 'photos' => ['https://images.unsplash.com/photo-1519641471654-76ce0107ad1b?w=800&q=80']],
            ['vehicule_vin' => 'SJNFDAJ11U1234567', 'titre' => 'Nissan Qashqai 2019 - Polyvalent', 'prix' => 13800000, 'photos' => ['https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&q=80']],
            ['vehicule_vin' => 'SALCA2AX4MH123456', 'titre' => 'Land Rover Defender 2022 - Tout-terrain', 'prix' => 55000000, 'photos' => ['https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=800&q=80']],
        ];

        foreach ($annonces as $annonceData) {
            $vehicule = Vehicule::where('vin', $annonceData['vehicule_vin'])->first();
            if ($vehicule) {
                Annonce::create([
                    'vendeur_id' => $vendeur->id,
                    'vehicule_id'=> $vehicule->id,
                    'titre'      => $annonceData['titre'],
                    'prix'       => $annonceData['prix'],
                    'statut'     => 'DISPONIBLE',
                    'photos'     => $annonceData['photos'],
                ]);
            }
        }
    }
}