<?php

namespace Database\Seeders;

use App\Models\Annonce;
use App\Models\Acheteur;
use App\Models\Avis;
use Illuminate\Database\Seeder;

class AvisSeeder extends Seeder
{
    public function run(): void
    {
        $commentaires = [
            // Commentaires 5 étoiles
            'Excellent vendeur, très professionnel. Le véhicule correspond exactement à la description. Je recommande vivement !',
            'Transaction fluide, véhicule en parfait état. Le vendeur a pris le temps de répondre à toutes mes questions. Très satisfait !',
            'Rien à redire, tout est parfait. Le véhicule est exactement comme décrit. Vendeur sérieux et fiable.',
            'Très bonne expérience d\'achat. Le véhicule est impeccable et le service client irréprochable.',
            'Super transaction ! Le vendeur est à l\'écoute et le véhicule est en excellent état. Je recommande les yeux fermés.',

            // Commentaires 4 étoiles
            'Bon vendeur, véhicule conforme. Quelques rayures mineures non mentionnées mais rien de grave.',
            'Bonne affaire dans l\'ensemble. Le véhicule correspond à la description, juste un petit délai de livraison.',
            'Vendeur sérieux, véhicule correct. Un peu de négociation aurait été apprécié mais satisfait dans l\'ensemble.',
            'Transaction satisfaisante. Le véhicule a quelques signes d\'usure mais c\'est acceptable pour le prix.',

            // Commentaires 3 étoiles
            'Moyen. Le véhicule fonctionne bien mais il y a quelques défauts esthétiques non mentionnés dans l\'annonce.',
            'Correct sans plus. Le vendeur aurait pu être plus transparent sur l\'état réel du véhicule.',
            'Véhicule acceptable pour le prix. Quelques points à revoir mais ça reste honnête.',
            'Transaction correcte mais j\'attendais un peu mieux au vu des photos. Le véhicule a plus de kilomètres que prévu.',
        ];

        $annonces = Annonce::with('vendeur')->get();
        $acheteurs = Acheteur::all();

        if ($acheteurs->count() < 6) {
            $this->command->error('Pas assez d\'acheteurs (minimum 6 requis)');
            return;
        }

        $notesDistribution = [5, 5, 5, 4, 4, 3];

        foreach ($annonces as $annonce) {
            $acheteursShuffle = $acheteurs->shuffle()->take(6);

            foreach ($acheteursShuffle as $index => $acheteur) {
                $note = $notesDistribution[$index];

                // Sélectionner un commentaire approprié selon la note
                if ($note === 5) {
                    $comment = $commentaires[array_rand(array_slice($commentaires, 0, 5))];
                } elseif ($note === 4) {
                    $comment = $commentaires[array_rand(array_slice($commentaires, 5, 4))];
                } else {
                    $comment = $commentaires[array_rand(array_slice($commentaires, 9, 4))];
                }

                Avis::firstOrCreate(
                    [
                        'acheteur_id' => $acheteur->id,
                        'annonce_id' => $annonce->id,
                    ],
                    [
                        'vendeur_id' => $annonce->vendeur_id,
                        'type' => 'AVIS_VENDEUR',
                        'note' => $note,
                        'commentaire' => $comment,
                        'signale' => false,
                        'statut' => 'APPROUVE',
                        'signale_par_vendeur' => false,
                    ]
                );

                $this->command->info("Avis créé pour annonce #{$annonce->id} - Note: {$note}/5");
            }
        }

        $this->command->info('✓ Seeder Avis terminé avec succès : 6 avis par annonce');
    }
}
