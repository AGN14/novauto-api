<?php

namespace App\Services;

use App\Models\Annonce;
use App\Models\Vehicule;
use App\Models\Vendeur;

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
        $vehicule = Vehicule::firstOrCreate(
            ['vin' => strtoupper($data['vin'])],
            [
                'modele_id'       => $data['modele_id'],
                'annee'           => $data['annee'],
                'kilometrage'     => $data['kilometrage'],
                'statut_douanier' => $data['statut_douanier'],
                'vin_verifie'     => false,
            ]
        );

        $annonce = Annonce::create([
            'vendeur_id'  => $vendeurId,
            'vehicule_id' => $vehicule->id,
            'titre'       => $data['titre'],
            'prix'        => $data['prix'],
            'statut'      => 'DISPONIBLE',
            'photos'      => $data['photos'],
        ]);

        return $annonce->load(['vehicule.modele.marque']);
    }

    public function update(Annonce $annonce, array $data): Annonce
    {
        $annonce->update(array_filter([
            'titre'       => $data['titre'] ?? null,
            'prix'        => $data['prix'] ?? null,
            'photos'      => $data['photos'] ?? null,
            'statut'      => $data['statut'] ?? null,
        ]));

        if (isset($data['statut_douanier']) || isset($data['kilometrage'])) {
            $annonce->vehicule->update(array_filter([
                'statut_douanier' => $data['statut_douanier'] ?? null,
                'kilometrage'     => $data['kilometrage'] ?? null,
            ]));
        }

        return $annonce->load(['vehicule.modele.marque']);
    }

    public function delete(Annonce $annonce): void
    {
        $annonce->delete();
    }
}