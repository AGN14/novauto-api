<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Disponibilite;
use App\Models\DisponibiliteGarage;
use App\Models\Vendeur;
use App\Models\GaragePartenaire;
use Carbon\Carbon;

class DisponibiliteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer des disponibilités pour les vendeurs
        $vendeurs = Vendeur::all();

        foreach ($vendeurs as $vendeur) {
            // Créer 14 jours de disponibilités (2 semaines)
            for ($i = 1; $i <= 14; $i++) {
                $jour = Carbon::today()->addDays($i);

                // Créneaux du matin
                Disponibilite::create([
                    'vendeur_id' => $vendeur->id,
                    'jour' => $jour,
                    'heure_debut' => '09:00',
                    'heure_fin' => '10:00',
                    'statut' => 'LIBRE'
                ]);

                Disponibilite::create([
                    'vendeur_id' => $vendeur->id,
                    'jour' => $jour,
                    'heure_debut' => '10:00',
                    'heure_fin' => '11:00',
                    'statut' => 'LIBRE'
                ]);

                Disponibilite::create([
                    'vendeur_id' => $vendeur->id,
                    'jour' => $jour,
                    'heure_debut' => '11:00',
                    'heure_fin' => '12:00',
                    'statut' => 'LIBRE'
                ]);

                // Créneaux de l'après-midi
                Disponibilite::create([
                    'vendeur_id' => $vendeur->id,
                    'jour' => $jour,
                    'heure_debut' => '14:00',
                    'heure_fin' => '15:00',
                    'statut' => 'LIBRE'
                ]);

                Disponibilite::create([
                    'vendeur_id' => $vendeur->id,
                    'jour' => $jour,
                    'heure_debut' => '15:00',
                    'heure_fin' => '16:00',
                    'statut' => 'LIBRE'
                ]);

                Disponibilite::create([
                    'vendeur_id' => $vendeur->id,
                    'jour' => $jour,
                    'heure_debut' => '16:00',
                    'heure_fin' => '17:00',
                    'statut' => 'LIBRE'
                ]);

                Disponibilite::create([
                    'vendeur_id' => $vendeur->id,
                    'jour' => $jour,
                    'heure_debut' => '17:00',
                    'heure_fin' => '18:00',
                    'statut' => 'LIBRE'
                ]);
            }
        }

        $this->command->info('Disponibilités vendeurs créées avec succès!');

        // Créer des disponibilités pour les garages
        $garages = GaragePartenaire::all();

        foreach ($garages as $garage) {
            // Créer 14 jours de disponibilités (2 semaines)
            for ($i = 1; $i <= 14; $i++) {
                $jour = Carbon::today()->addDays($i);

                // Créneaux du matin
                DisponibiliteGarage::create([
                    'garage_id' => $garage->id,
                    'jour' => $jour,
                    'heure_debut' => '08:00',
                    'heure_fin' => '09:00',
                    'statut' => 'LIBRE'
                ]);

                DisponibiliteGarage::create([
                    'garage_id' => $garage->id,
                    'jour' => $jour,
                    'heure_debut' => '09:00',
                    'heure_fin' => '10:00',
                    'statut' => 'LIBRE'
                ]);

                DisponibiliteGarage::create([
                    'garage_id' => $garage->id,
                    'jour' => $jour,
                    'heure_debut' => '10:00',
                    'heure_fin' => '11:00',
                    'statut' => 'LIBRE'
                ]);

                // Créneaux de l'après-midi
                DisponibiliteGarage::create([
                    'garage_id' => $garage->id,
                    'jour' => $jour,
                    'heure_debut' => '14:00',
                    'heure_fin' => '15:00',
                    'statut' => 'LIBRE'
                ]);

                DisponibiliteGarage::create([
                    'garage_id' => $garage->id,
                    'jour' => $jour,
                    'heure_debut' => '15:00',
                    'heure_fin' => '16:00',
                    'statut' => 'LIBRE'
                ]);

                DisponibiliteGarage::create([
                    'garage_id' => $garage->id,
                    'jour' => $jour,
                    'heure_debut' => '16:00',
                    'heure_fin' => '17:00',
                    'statut' => 'LIBRE'
                ]);
            }
        }

        $this->command->info('Disponibilités garages créées avec succès!');
    }
}
