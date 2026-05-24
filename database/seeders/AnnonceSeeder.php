<?php

namespace Database\Seeders;

use App\Models\Annonce;
use App\Models\Vehicule;
use App\Models\Vendeur;
use App\Models\Marque;
use App\Models\Modele;
use Illuminate\Database\Seeder;

class AnnonceSeeder extends Seeder
{
    private $villes = ['Cotonou', 'Porto-Novo', 'Parakou', 'Abomey-Calavi', 'Bohicon'];
    private $villeIndex = 0;

    public function run(): void
    {
        $vendeurs = Vendeur::all();

        if ($vendeurs->isEmpty()) {
            $this->command->error('Aucun vendeur trouvé. Exécutez VendeurSeeder d\'abord.');
            return;
        }

        $vehicules = [
            // AUDI (4)
            ['marque' => 'Audi', 'modele' => 'A4', 'annee' => 2021, 'km' => 28500, 'prix' => 22500000, 'titre' => 'Audi A4 2021 — Élégance et Performance', 'vin' => 'WAUZZZ8V8KA012345', 'douane' => 'DEDOUANE'],
            ['marque' => 'Audi', 'modele' => 'Q5', 'annee' => 2022, 'km' => 15000, 'prix' => 35000000, 'titre' => 'Audi Q5 2022 — SUV Premium', 'vin' => 'WA1LZZBF1KD056789', 'douane' => 'DEDOUANE'],
            ['marque' => 'Audi', 'modele' => 'A6', 'annee' => 2020, 'km' => 42000, 'prix' => 28000000, 'titre' => 'Audi A6 2020 — Berline Luxe', 'vin' => 'WAUZZZ4G0KN012345', 'douane' => 'EN_TRANSIT'],
            ['marque' => 'Audi', 'modele' => 'Q7', 'annee' => 2023, 'km' => 8500, 'prix' => 52000000, 'titre' => 'Audi Q7 2023 — SUV 7 Places', 'vin' => 'WA1AZZBF4KD098765', 'douane' => 'DEDOUANE'],

            // BMW (4)
            ['marque' => 'BMW', 'modele' => 'Série 3', 'annee' => 2021, 'km' => 32000, 'prix' => 25000000, 'titre' => 'BMW Série 3 2021 — Sportive et Élégante', 'vin' => 'WBA8B9C54K7A01234', 'douane' => 'DEDOUANE'],
            ['marque' => 'BMW', 'modele' => 'X5', 'annee' => 2022, 'km' => 18500, 'prix' => 48000000, 'titre' => 'BMW X5 2022 — SUV de Prestige', 'vin' => 'WBAJX0C52KWW12345', 'douane' => 'DEDOUANE'],
            ['marque' => 'BMW', 'modele' => 'Série 5', 'annee' => 2020, 'km' => 45000, 'prix' => 32000000, 'titre' => 'BMW Série 5 2020 — Business Class', 'vin' => 'WBA5B5C50KAF67890', 'douane' => 'EN_TRANSIT'],
            ['marque' => 'BMW', 'modele' => 'X3', 'annee' => 2023, 'km' => 12000, 'prix' => 38000000, 'titre' => 'BMW X3 2023 — Compact Premium', 'vin' => 'WBSXXX0C03KW23456', 'douane' => 'DEDOUANE'],

            // CUPRA (4)
            ['marque' => 'Cupra', 'modele' => 'Formentor', 'annee' => 2022, 'km' => 22000, 'prix' => 18500000, 'titre' => 'Cupra Formentor 2022 — Design Sportif', 'vin' => 'VZ3CBBHH0N7000123', 'douane' => 'DEDOUANE'],
            ['marque' => 'Cupra', 'modele' => 'Born', 'annee' => 2023, 'km' => 9500, 'prix' => 16000000, 'titre' => 'Cupra Born 2023 — Électrique Sportive', 'vin' => 'VZ3BBBHE2N7001234', 'douane' => 'EN_TRANSIT'],
            ['marque' => 'Cupra', 'modele' => 'Ateca', 'annee' => 2021, 'km' => 35000, 'prix' => 15000000, 'titre' => 'Cupra Ateca 2021 — SUV Dynamique', 'vin' => 'VZ3CBAHH8M7002345', 'douane' => 'DEDOUANE'],
            ['marque' => 'Cupra', 'modele' => 'Leon', 'annee' => 2022, 'km' => 28000, 'prix' => 14500000, 'titre' => 'Cupra Leon 2022 — Compacte Sportive', 'vin' => 'VZ3BAAHH5N7003456', 'douane' => 'DEDOUANE'],

            // FORD (4)
            ['marque' => 'Ford', 'modele' => 'Mustang', 'annee' => 2022, 'km' => 15000, 'prix' => 32000000, 'titre' => 'Ford Mustang 2022 — Légende Américaine', 'vin' => '1FA6P8CF4N5100123', 'douane' => 'DEDOUANE'],
            ['marque' => 'Ford', 'modele' => 'Explorer', 'annee' => 2021, 'km' => 38000, 'prix' => 28500000, 'titre' => 'Ford Explorer 2021 — SUV 7 Places', 'vin' => '1FM5K8D88MGC01234', 'douane' => 'EN_TRANSIT'],
            ['marque' => 'Ford', 'modele' => 'Ranger', 'annee' => 2022, 'km' => 42000, 'prix' => 22000000, 'titre' => 'Ford Ranger 2022 — Pick-up Robuste', 'vin' => '1FTEW1EP2NFC02345', 'douane' => 'DEDOUANE'],
            ['marque' => 'Ford', 'modele' => 'Puma', 'annee' => 2023, 'km' => 8000, 'prix' => 14000000, 'titre' => 'Ford Puma 2023 — Compact Moderne', 'vin' => 'WMEEJ5BA0N7003456', 'douane' => 'DEDOUANE'],

            // MERCEDES (5)
            ['marque' => 'Mercedes-Benz', 'modele' => 'Classe C', 'annee' => 2022, 'km' => 22000, 'prix' => 35000000, 'titre' => 'Mercedes Classe C 2022 — Raffinement Absolu', 'vin' => 'WDD2050882R104567', 'douane' => 'DEDOUANE'],
            ['marque' => 'Mercedes-Benz', 'modele' => 'GLE', 'annee' => 2021, 'km' => 28500, 'prix' => 58000000, 'titre' => 'Mercedes GLE 2021 — SUV Luxe', 'vin' => 'WDC1671161A205678', 'douane' => 'DEDOUANE'],
            ['marque' => 'Mercedes-Benz', 'modele' => 'Classe A', 'annee' => 2023, 'km' => 12000, 'prix' => 22000000, 'titre' => 'Mercedes Classe A 2023 — Compacte Premium', 'vin' => 'WDD1770401N306789', 'douane' => 'EN_TRANSIT'],
            ['marque' => 'Mercedes-Benz', 'modele' => 'GLC', 'annee' => 2022, 'km' => 19500, 'prix' => 45000000, 'titre' => 'Mercedes GLC 2022 — SUV Élégant', 'vin' => 'WDC2539621F407890', 'douane' => 'DEDOUANE'],
            ['marque' => 'Mercedes-Benz', 'modele' => 'Classe E', 'annee' => 2021, 'km' => 35000, 'prix' => 42000000, 'titre' => 'Mercedes Classe E 2021 — Berline Executive', 'vin' => 'WDD2130591A508901', 'douane' => 'DEDOUANE'],

            // OPEL (4)
            ['marque' => 'Opel', 'modele' => 'Astra', 'annee' => 2022, 'km' => 25000, 'prix' => 12000000, 'titre' => 'Opel Astra 2022 — Compacte Fiable', 'vin' => 'W0VSX92125W109012', 'douane' => 'DEDOUANE'],
            ['marque' => 'Opel', 'modele' => 'Crossland', 'annee' => 2021, 'km' => 38000, 'prix' => 10500000, 'titre' => 'Opel Crossland 2021 — SUV Urbain', 'vin' => 'W0VGZ78925S210123', 'douane' => 'EN_TRANSIT'],
            ['marque' => 'Opel', 'modele' => 'Grandland', 'annee' => 2023, 'km' => 15000, 'prix' => 15000000, 'titre' => 'Opel Grandland 2023 — SUV Moderne', 'vin' => 'W0VRZ78HTPS311234', 'douane' => 'DEDOUANE'],
            ['marque' => 'Opel', 'modele' => 'Mokka', 'annee' => 2022, 'km' => 22000, 'prix' => 13500000, 'titre' => 'Opel Mokka 2022 — Style et Praticité', 'vin' => 'W0VSX98135W412345', 'douane' => 'DEDOUANE'],

            // RENAULT (4)
            ['marque' => 'Renault', 'modele' => 'Clio', 'annee' => 2022, 'km' => 28000, 'prix' => 9500000, 'titre' => 'Renault Clio 2022 — Citadine Économique', 'vin' => 'VF15RZBN065513456', 'douane' => 'DEDOUANE'],
            ['marque' => 'Renault', 'modele' => 'Captur', 'annee' => 2021, 'km' => 35000, 'prix' => 11000000, 'titre' => 'Renault Captur 2021 — SUV Compact', 'vin' => 'VF16R5AH465614567', 'douane' => 'EN_TRANSIT'],
            ['marque' => 'Renault', 'modele' => 'Megane', 'annee' => 2022, 'km' => 24000, 'prix' => 12500000, 'titre' => 'Renault Megane 2022 — Berline Confortable', 'vin' => 'VF1BZ0W0465715678', 'douane' => 'DEDOUANE'],
            ['marque' => 'Renault', 'modele' => 'Arkana', 'annee' => 2023, 'km' => 12000, 'prix' => 14000000, 'titre' => 'Renault Arkana 2023 — Coupé-SUV Innovant', 'vin' => 'VF19Z0W1565816789', 'douane' => 'DEDOUANE'],

            // SKODA (4)
            ['marque' => 'Skoda', 'modele' => 'Octavia', 'annee' => 2021, 'km' => 32000, 'prix' => 13000000, 'titre' => 'Skoda Octavia 2021 — Spacieuse et Pratique', 'vin' => 'TMBJJ7NE5M7917890', 'douane' => 'DEDOUANE'],
            ['marque' => 'Skoda', 'modele' => 'Kodiaq', 'annee' => 2022, 'km' => 22000, 'prix' => 18000000, 'titre' => 'Skoda Kodiaq 2022 — SUV 7 Places', 'vin' => 'TMBNU7NP5N7018901', 'douane' => 'EN_TRANSIT'],
            ['marque' => 'Skoda', 'modele' => 'Karoq', 'annee' => 2023, 'km' => 15000, 'prix' => 15500000, 'titre' => 'Skoda Karoq 2023 — Compact Familial', 'vin' => 'TMBJG7NE8P7119012', 'douane' => 'DEDOUANE'],
            ['marque' => 'Skoda', 'modele' => 'Superb', 'annee' => 2021, 'km' => 38000, 'prix' => 16000000, 'titre' => 'Skoda Superb 2021 — Grande Berline', 'vin' => 'TMBCT6NP5M7220123', 'douane' => 'DEDOUANE'],

            // TESLA (4)
            ['marque' => 'Tesla', 'modele' => 'Model 3', 'annee' => 2022, 'km' => 18000, 'prix' => 28000000, 'titre' => 'Tesla Model 3 2022 — Électrique Performante', 'vin' => '5YJ3E1EA8NF021234', 'douane' => 'DEDOUANE'],
            ['marque' => 'Tesla', 'modele' => 'Model Y', 'annee' => 2023, 'km' => 10000, 'prix' => 35000000, 'titre' => 'Tesla Model Y 2023 — SUV Électrique', 'vin' => '7SAYGDEE6PF022345', 'douane' => 'DEDOUANE'],
            ['marque' => 'Tesla', 'modele' => 'Model S', 'annee' => 2021, 'km' => 25000, 'prix' => 55000000, 'titre' => 'Tesla Model S 2021 — Berline Haut de Gamme', 'vin' => '5YJSA1E14MF023456', 'douane' => 'EN_TRANSIT'],
            ['marque' => 'Tesla', 'modele' => 'Model X', 'annee' => 2022, 'km' => 15000, 'prix' => 62000000, 'titre' => 'Tesla Model X 2022 — SUV 7 Places Électrique', 'vin' => '5YJXCBE24NF024567', 'douane' => 'DEDOUANE'],

            // TOYOTA (5)
            ['marque' => 'Toyota', 'modele' => 'RAV4', 'annee' => 2022, 'km' => 28000, 'prix' => 18500000, 'titre' => 'Toyota RAV4 2022 — SUV Fiable', 'vin' => 'JTMN1RFV8ND025678', 'douane' => 'DEDOUANE'],
            ['marque' => 'Toyota', 'modele' => 'Land Cruiser', 'annee' => 2021, 'km' => 35000, 'prix' => 65000000, 'titre' => 'Toyota Land Cruiser 2021 — Légende Tout-Terrain', 'vin' => 'JTMHX09J0M5026789', 'douane' => 'DEDOUANE'],
            ['marque' => 'Toyota', 'modele' => 'Corolla', 'annee' => 2023, 'km' => 8000, 'prix' => 12000000, 'titre' => 'Toyota Corolla 2023 — Berline Économique', 'vin' => 'JTDBL40E5P3027890', 'douane' => 'EN_TRANSIT'],
            ['marque' => 'Toyota', 'modele' => 'Highlander', 'annee' => 2022, 'km' => 22000, 'prix' => 35000000, 'titre' => 'Toyota Highlander 2022 — SUV 7 Places', 'vin' => 'JTEHJ3EW2NA028901', 'douane' => 'DEDOUANE'],
            ['marque' => 'Toyota', 'modele' => 'C-HR', 'annee' => 2023, 'km' => 12000, 'prix' => 14500000, 'titre' => 'Toyota C-HR 2023 — SUV Compact', 'vin' => 'NMTKHMBX8PR029012', 'douane' => 'DEDOUANE'],

            // VOLVO (4)
            ['marque' => 'Volvo', 'modele' => 'XC60', 'annee' => 2022, 'km' => 24000, 'prix' => 38000000, 'titre' => 'Volvo XC60 2022 — SUV Sécurité Max', 'vin' => 'YV1RSACKXN2030123', 'douane' => 'DEDOUANE'],
            ['marque' => 'Volvo', 'modele' => 'XC90', 'annee' => 2021, 'km' => 32000, 'prix' => 55000000, 'titre' => 'Volvo XC90 2021 — SUV Premium 7 Places', 'vin' => 'YV4H60DK9M1031234', 'douane' => 'EN_TRANSIT'],
            ['marque' => 'Volvo', 'modele' => 'V60', 'annee' => 2023, 'km' => 15000, 'prix' => 28000000, 'titre' => 'Volvo V60 2023 — Break Élégant', 'vin' => 'YV1DZ8256P1032345', 'douane' => 'DEDOUANE'],
            ['marque' => 'Volvo', 'modele' => 'S60', 'annee' => 2022, 'km' => 22000, 'prix' => 25000000, 'titre' => 'Volvo S60 2022 — Berline Sportive', 'vin' => 'YV1A22RK6N2033456', 'douane' => 'DEDOUANE'],

            // VOLKSWAGEN (4)
            ['marque' => 'Volkswagen', 'modele' => 'Golf', 'annee' => 2022, 'km' => 28000, 'prix' => 14500000, 'titre' => 'Volkswagen Golf 2022 — Compacte Polyvalente', 'vin' => 'WVWZZZCDZNW034567', 'douane' => 'DEDOUANE'],
            ['marque' => 'Volkswagen', 'modele' => 'Tiguan', 'annee' => 2021, 'km' => 35000, 'prix' => 20000000, 'titre' => 'Volkswagen Tiguan 2021 — SUV Familial', 'vin' => 'WVGZZZNU9MW035678', 'douane' => 'EN_TRANSIT'],
            ['marque' => 'Volkswagen', 'modele' => 'Passat', 'annee' => 2022, 'km' => 24000, 'prix' => 18000000, 'titre' => 'Volkswagen Passat 2022 — Berline Spacieuse', 'vin' => 'WVWZZZ3CZNE036789', 'douane' => 'DEDOUANE'],
            ['marque' => 'Volkswagen', 'modele' => 'T-Roc', 'annee' => 2023, 'km' => 12000, 'prix' => 16500000, 'titre' => 'Volkswagen T-Roc 2023 — SUV Urbain', 'vin' => 'WVGZZZCFXPW037890', 'douane' => 'DEDOUANE'],
        ];

        $vendeurIndex = 0;
        $photoPool = [
            'https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?w=800&q=80',
            'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&q=80',
            'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=800&q=80',
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&q=80',
            'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&q=80',
            'https://images.unsplash.com/photo-1583121274602-3e2820c69888?w=800&q=80',
            'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?w=800&q=80',
            'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&q=80',
            'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?w=800&q=80',
            'https://images.unsplash.com/photo-1561049501-e1f96bdd98fd?w=800&q=80',
            'https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=800&q=80',
            'https://images.unsplash.com/photo-1502877338535-766e1452684a?w=800&q=80',
            'https://images.unsplash.com/photo-1489824904134-891ab64532f1?w=800&q=80',
            'https://images.unsplash.com/photo-1471444928139-48c5bf5173f8?w=800&q=80',
            'https://images.unsplash.com/photo-1603386329225-868f9b1ee6c9?w=800&q=80',
        ];

        foreach ($vehicules as $index => $data) {
            // Créer ou récupérer la marque
            $marque = Marque::firstOrCreate(['nom' => $data['marque']]);

            // Créer ou récupérer le modèle
            $modele = Modele::firstOrCreate(
                ['nom' => $data['modele'], 'marque_id' => $marque->id]
            );

            // Créer le véhicule
            $vehicule = Vehicule::create([
                'modele_id'       => $modele->id,
                'vin'             => $data['vin'],
                'annee'           => $data['annee'],
                'kilometrage'     => $data['km'],
                'statut_douanier' => $data['douane'],
                'vin_verifie'     => true,
            ]);

            // Sélectionner 5 photos aléatoires
            $photos = [];
            $photoIndexes = array_rand($photoPool, 5);
            foreach ($photoIndexes as $photoIndex) {
                $photos[] = $photoPool[$photoIndex];
            }

            // Récupérer vendeur en rotation
            $vendeur = $vendeurs[$vendeurIndex % $vendeurs->count()];
            $vendeurIndex++;

            // Créer l'annonce
            Annonce::create([
                'vendeur_id'          => $vendeur->id,
                'vehicule_id'         => $vehicule->id,
                'titre'               => $data['titre'],
                'prix'                => $data['prix'],
                'montant_reservation' => rand(50000, 100000), // Entre 50k et 100k FCFA
                'statut'              => 'DISPONIBLE',
                'ville'               => $this->getNextVille(),
                'description'         => "Superbe {$data['marque']} {$data['modele']} en excellent état. Véhicule entretenu régulièrement avec historique complet. Idéal pour une utilisation quotidienne ou professionnelle.",
                'equipements'         => ['GPS', 'Caméra de recul', 'Climatisation', 'Sièges en cuir', 'Régulateur de vitesse'],
                'photos'              => $photos,
            ]);

            $this->command->info("Annonce créée : {$data['titre']}");
        }

        $this->command->info('✅ 50 annonces créées avec succès !');
    }

    private function getNextVille(): string
    {
        $ville = $this->villes[$this->villeIndex];
        $this->villeIndex = ($this->villeIndex + 1) % count($this->villes);
        return $ville;
    }
}
