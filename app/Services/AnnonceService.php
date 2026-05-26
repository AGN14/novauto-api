<?php

namespace App\Services;

use App\Models\Annonce;
use App\Models\Vehicule;
use App\Models\Vendeur;
use App\Models\Marque;
use App\Models\Modele;

class AnnonceService
{
    public function getMesAnnonces(int $vendeurId): array
    {
        $annonces = Annonce::with(['vehicule.modele.marque'])
            ->where('vendeur_id', $vendeurId)
            ->orderBy('created_at', 'desc')
            ->get();

        return $annonces->toArray();
    }

    public function create(array $data, int $vendeurId): Annonce
    {
        // Créer ou récupérer la marque
        $marque = Marque::firstOrCreate(
            ['nom' => strtoupper($data['marque'])],
            ['logo' => null]
        );

        // Créer ou récupérer le modèle
        $modeleNom = $data['modele'] ?? 'Modèle inconnu';
        $modele = Modele::firstOrCreate(
            [
                'marque_id' => $marque->id,
                'nom' => $modeleNom
            ],
            [
                'carburant' => $data['carburant'] ?? null,
                'transmission' => $data['transmission'] ?? null,
            ]
        );

        // Créer ou récupérer le véhicule
        $vehicule = Vehicule::firstOrCreate(
            ['vin' => strtoupper($data['vin'])],
            [
                'modele_id'       => $modele->id,
                'annee'           => $data['annee'],
                'kilometrage'     => $data['kilometrage'],
                'statut_douanier' => $data['statut_douanier'],
                'vin_verifie'     => true,
                'pays_origine'    => $data['pays_origine'] ?? null,
            ]
        );

        // Créer l'annonce
        $annonce = Annonce::create([
            'vendeur_id'          => $vendeurId,
            'vehicule_id'         => $vehicule->id,
            'titre'               => $data['titre'],
            'prix'                => $data['prix'],
            'montant_reservation' => $data['montant_reservation'] ?? null,
            'statut'              => 'DISPONIBLE',
            'photos'              => $data['photos'],
            'description'         => $data['description'] ?? null,
            'equipements'         => $data['equipements'] ?? [],
            'ville'               => $data['ville'] ?? null,
        ]);

        return $annonce->load(['vehicule.modele.marque']);
    }

    public function update(Annonce $annonce, array $data): Annonce
    {
        $annonce->update(array_filter([
            'titre'               => $data['titre'] ?? null,
            'prix'                => $data['prix'] ?? null,
            'montant_reservation' => $data['montant_reservation'] ?? null,
            'photos'              => $data['photos'] ?? null,
            'statut'              => $data['statut'] ?? null,
            'description'         => $data['description'] ?? null,
            'ville'               => $data['ville'] ?? null,
        ], fn($value) => $value !== null));

        if (isset($data['statut_douanier']) || isset($data['kilometrage'])) {
            $annonce->vehicule->update(array_filter([
                'statut_douanier' => $data['statut_douanier'] ?? null,
                'kilometrage'     => $data['kilometrage'] ?? null,
            ], fn($value) => $value !== null));
        }

        return $annonce->load(['vehicule.modele.marque']);
    }

    public function delete(Annonce $annonce): void
    {
        $annonce->delete();
    }
}