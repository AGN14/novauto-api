<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GaragePartenaire;
use App\Models\RapportInspection;
use App\Models\Annonce;
use App\Models\Notification;
use App\Models\Vehicule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InspectionController extends Controller
{
    public function garages(): JsonResponse
    {
        $garages = GaragePartenaire::where('agree', true)
            ->orderBy('ville')
            ->orderBy('nom')
            ->get();

        return response()->json($garages);
    }

    public function demanderInspection(Request $request): JsonResponse
    {
        $request->validate([
            'annonce_id' => ['required', 'exists:annonces,id'],
            'garage_id'  => ['required', 'exists:garages_partenaires,id'],
            'date_rdv'   => ['nullable', 'date'],
            'heure_rdv'  => ['nullable', 'date_format:H:i:s'],
        ]);

        $annonce = Annonce::findOrFail($request->annonce_id);
        $vendeur = $request->user()->vendeur;

        if ($annonce->vendeur_id !== $vendeur->id) {
            return response()->json(['message' => 'Action non autorisée.'], 403);
        }

        $existant = RapportInspection::where('annonce_id', $annonce->id)
            ->whereIn('statut', ['EN_ATTENTE', 'EN_COURS', 'VALIDEE'])
            ->first();

        if ($existant) {
            return response()->json(['message' => 'Une inspection est déjà en cours ou validée pour cette annonce.'], 422);
        }

        $rapport = RapportInspection::create([
            'annonce_id'      => $annonce->id,
            'vehicule_id'     => $annonce->vehicule_id,
            'garage_id'       => $request->garage_id,
            'date_rdv'        => $request->date_rdv,
            'heure_rdv'       => $request->heure_rdv,
            'statut'          => 'EN_ATTENTE',
            'date_soumission' => now(),
        ]);

        // Marquer le créneau comme occupé si date/heure fournis
        if ($request->date_rdv && $request->heure_rdv) {
            $disponibiliteController = new DisponibiliteController();
            $disponibiliteController->occuperCreneauGarage(
                $request->garage_id,
                $request->date_rdv,
                $request->heure_rdv
            );
        }

        return response()->json([
            'message' => 'Demande d\'inspection envoyée avec succès.',
            'rapport' => $rapport->load('garage'),
        ], 201);
    }

    public function mesInspections(Request $request): JsonResponse
    {
        $vendeur = $request->user()->vendeur;

        $rapports = RapportInspection::with(['garage', 'annonce'])
            ->whereHas('annonce', function ($q) use ($vendeur) {
                $q->where('vendeur_id', $vendeur->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($rapports);
    }

    /**
     * Liste des demandes d'inspection EN_ATTENTE pour le garage connecté
     */
    public function demandesEnAttente(Request $request): JsonResponse
    {
        $garage = $request->user(); // Le garage connecté

        $demandes = RapportInspection::with([
            'annonce.vehicule.modele.marque',
            'annonce.vendeur.user'
        ])
            ->where('garage_id', $garage->id)
            ->where('statut', 'EN_ATTENTE')
            ->orderBy('date_soumission', 'desc')
            ->get();

        return response()->json($demandes);
    }

    /**
     * Garage soumet le rapport d'inspection
     */
    public function soumettreRapport(Request $request, int $id): JsonResponse
    {
        $garage = $request->user();

        $rapport = RapportInspection::with(['annonce.vendeur.user', 'annonce.vehicule'])
            ->where('garage_id', $garage->id)
            ->findOrFail($id);

        if ($rapport->statut !== 'EN_ATTENTE') {
            return response()->json([
                'message' => 'Cette inspection n\'est plus en attente.'
            ], 422);
        }

        // Vérifier que la présence du vendeur est confirmée
        if (!$rapport->presence_confirmee) {
            return response()->json([
                'message' => 'La présence du vendeur doit être confirmée avant de soumettre le rapport.'
            ], 422);
        }

        $validated = $request->validate([
            'etat_carrosserie'    => ['required', 'in:EXCELLENT,BON,MOYEN,MAUVAIS'],
            'etat_moteur'         => ['required', 'in:EXCELLENT,BON,MOYEN,MAUVAIS'],
            'etat_freins'         => ['required', 'in:EXCELLENT,BON,MOYEN,MAUVAIS'],
            'etat_pneus'          => ['required', 'in:EXCELLENT,BON,MOYEN,MAUVAIS'],
            'kilometrage_verifie' => ['required', 'integer', 'min:0'],
            'observations'        => ['nullable', 'string', 'max:2000'],
            'photos'              => ['required', 'array', 'min:2'],
            'photos.*'            => ['required', 'string'],
        ]);

        // Mettre à jour le rapport
        $rapport->update([
            'etat_carrosserie'    => $validated['etat_carrosserie'],
            'etat_moteur'         => $validated['etat_moteur'],
            'etat_freins'         => $validated['etat_freins'],
            'etat_pneus'          => $validated['etat_pneus'],
            'kilometrage_verifie' => $validated['kilometrage_verifie'],
            'observations'        => $validated['observations'] ?? null,
            'photos_inspection'   => $validated['photos'],
            'statut'              => 'VALIDEE',
            'date_inspection'     => now(),
            'date_validation'     => now(),
        ]);

        // Marquer le véhicule comme inspecté
        $vehicule = $rapport->annonce->vehicule;
        $vehicule->update(['inspecte' => true]);

        // Notifier le vendeur
        Notification::create([
            'destinataire_id' => $rapport->annonce->vendeur->user_id,
            'type'            => 'RAPPORT_VALIDE',
            'titre'           => 'Inspection terminée',
            'message'         => "Votre véhicule a été inspecté par {$garage->nom}.",
            'lien'            => "/vendeur/annonces/{$rapport->annonce_id}",
            'lu'              => false,
        ]);

        return response()->json([
            'message' => 'Rapport d\'inspection soumis avec succès.',
            'rapport' => $rapport->fresh(['garage', 'annonce.vehicule']),
        ]);
    }

    /**
     * Garage génère un code de présence
     */
    public function garageGenererCode(Request $request, int $id): JsonResponse
    {
        $garage = $request->user();

        $rapport = RapportInspection::with(['annonce.vendeur.user'])
            ->where('garage_id', $garage->id)
            ->findOrFail($id);

        if ($rapport->statut !== 'EN_ATTENTE') {
            return response()->json([
                'message' => 'Cette inspection n\'est plus en attente.'
            ], 422);
        }

        // Générer un code aléatoire de 6 caractères alphanumériques
        $code = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6));

        $now = now();
        $expireAt = $now->copy()->addMinutes(15);

        $rapport->update([
            'code_presence' => $code,
            'code_genere_at' => $now,
            'code_expire_at' => $expireAt,
        ]);

        // Notifier le vendeur
        Notification::create([
            'destinataire_id' => $rapport->annonce->vendeur->user_id,
            'type' => 'CODE_PRESENCE',
            'titre' => 'Code de présence généré',
            'message' => "Le garage {$garage->nom} a généré un code de présence : {$code}. Confirmez votre présence depuis votre espace vendeur.",
            'lien' => "/vendeur/inspections",
            'lu' => false,
        ]);

        return response()->json([
            'message' => 'Code de présence généré avec succès.',
            'code' => $code,
            'expire_at' => $expireAt->toISOString(),
        ]);
    }

    /**
     * Vendeur confirme sa présence avec le code
     */
    public function vendeurConfirmerPresence(Request $request, int $id): JsonResponse
    {
        $vendeur = $request->user()->vendeur;

        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $rapport = RapportInspection::with(['garage', 'annonce'])
            ->whereHas('annonce', function ($q) use ($vendeur) {
                $q->where('vendeur_id', $vendeur->id);
            })
            ->findOrFail($id);

        if (is_null($rapport->code_presence)) {
            return response()->json([
                'message' => 'Aucun code de présence n\'a été généré pour cette inspection.'
            ], 422);
        }

        if (strtoupper($request->code) !== $rapport->code_presence) {
            return response()->json([
                'message' => 'Le code saisi est incorrect.'
            ], 422);
        }

        if (now()->isAfter($rapport->code_expire_at)) {
            return response()->json([
                'message' => 'Le code a expiré. Demandez au garage de générer un nouveau code.'
            ], 422);
        }

        if ($rapport->presence_confirmee) {
            return response()->json([
                'message' => 'Votre présence a déjà été confirmée.'
            ], 422);
        }

        $rapport->update([
            'presence_confirmee' => true,
        ]);

        // Notifier le garage
        Notification::create([
            'destinataire_id' => $rapport->garage->user_id ?? null,
            'type' => 'PRESENCE_CONFIRMEE',
            'titre' => 'Présence confirmée',
            'message' => "Le vendeur {$request->user()->nom} a confirmé sa présence. Vous pouvez maintenant inspecter le véhicule.",
            'lien' => "/garage/inspections/{$id}",
            'lu' => false,
        ]);

        return response()->json([
            'message' => 'Votre présence a été confirmée avec succès.',
            'rapport' => $rapport->fresh(['garage', 'annonce']),
        ]);
    }

    /**
     * Garage rejette l'inspection
     */
    public function rejeterInspection(Request $request, int $id): JsonResponse
    {
        $garage = $request->user();

        $rapport = RapportInspection::with(['annonce.vendeur.user'])
            ->where('garage_id', $garage->id)
            ->findOrFail($id);

        if ($rapport->statut !== 'EN_ATTENTE') {
            return response()->json([
                'message' => 'Cette inspection n\'est plus en attente.'
            ], 422);
        }

        $validated = $request->validate([
            'motif' => ['required', 'string', 'max:500'],
        ]);

        $rapport->update([
            'statut'       => 'REJETEE',
            'observations' => $validated['motif'],
            'date_validation' => now(),
        ]);

        // Libérer le créneau si date/heure existent
        if ($rapport->date_rdv && $rapport->heure_rdv) {
            $disponibiliteController = new DisponibiliteController();
            $disponibiliteController->libererCreneauGarage(
                $rapport->garage_id,
                $rapport->date_rdv,
                $rapport->heure_rdv
            );
        }

        // Notifier le vendeur
        Notification::create([
            'destinataire_id' => $rapport->annonce->vendeur->user_id,
            'type'            => 'RAPPORT_SOUMIS',
            'titre'           => 'Inspection refusée',
            'message'         => "L'inspection de votre véhicule a été refusée par {$garage->nom}. Motif : {$validated['motif']}",
            'lien'            => "/vendeur/annonces/{$rapport->annonce_id}",
            'lu'              => false,
        ]);

        return response()->json([
            'message' => 'Inspection rejetée.',
            'rapport' => $rapport->fresh(['garage', 'annonce']),
        ]);
    }
}