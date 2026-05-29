<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use App\Models\RendezVous;
use App\Models\Reservation;
use App\Models\Notification;
use App\Models\Disponibilite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RendezVousController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        \Log::info('RendezVous store - Données reçues:', $request->all());

        $request->validate([
            'annonce_id' => 'required|exists:annonces,id',
            'date_rdv' => 'required|date|after_or_equal:today',
            'heure_rdv' => 'required|date_format:H:i',
            'message' => 'nullable|string|max:500',
        ]);

        \Log::info('RendezVous store - Validation passée');

        $acheteurId = $request->user()->acheteur->id;

        $existingRdv = RendezVous::where('acheteur_id', $acheteurId)
            ->where('annonce_id', $request->annonce_id)
            ->whereIn('statut', ['EN_ATTENTE', 'CONFIRME', 'AUTRE_DATE_PROPOSEE'])
            ->first();

        if ($existingRdv) {
            return response()->json(['message' => 'Vous avez déjà un rendez-vous pour ce véhicule.'], 422);
        }

        $existingReservation = Reservation::where('acheteur_id', $acheteurId)
            ->where('annonce_id', $request->annonce_id)
            ->whereIn('statut', ['EN_ATTENTE', 'CONFIRMEE'])
            ->first();

        if ($existingReservation) {
            return response()->json(['message' => 'Vous avez déjà une réservation pour ce véhicule.'], 422);
        }

        // Trouver la disponibilité correspondante
        $annonce = Annonce::findOrFail($request->annonce_id);
        $disponibilite = Disponibilite::where('vendeur_id', $annonce->vendeur_id)
            ->where('jour', $request->date_rdv)
            ->where('heure_debut', $request->heure_rdv)
            ->where('statut', 'LIBRE')
            ->first();

        if (!$disponibilite) {
            return response()->json([
                'message' => 'Ce créneau n\'est plus disponible'
            ], 422);
        }

        $rendezVous = RendezVous::create([
            'acheteur_id' => $acheteurId,
            'annonce_id' => $request->annonce_id,
            'disponibilite_id' => $disponibilite->id,
            'date_rdv' => $request->date_rdv,
            'heure_rdv' => $request->heure_rdv,
            'message' => $request->message,
            'statut' => 'EN_ATTENTE',
        ]);

        // Marquer la disponibilité comme OCCUPEE
        $disponibilite->update(['statut' => 'OCCUPE']);

        $annonce = Annonce::with('vendeur.user', 'vehicule.modele.marque')->find($request->annonce_id);
        $vehiculeNom = "{$annonce->vehicule->modele->marque->nom} {$annonce->vehicule->modele->nom}";

        \Log::info('Creating notification for rendez-vous', [
            'vendeur_user_id' => $annonce->vendeur->user_id,
            'vehicule' => $vehiculeNom
        ]);

        Notification::creer(
            $annonce->vendeur->user_id,
            'Nouvelle demande de rendez-vous',
            "a demandé un rendez-vous pour votre {$vehiculeNom}.",
            'RENDEZ_VOUS',
            '/vendeur/reservations',
            $request->user()->id  // expediteur_id = l'acheteur qui demande
        );

        return response()->json($rendezVous->load(['annonce.vehicule.modele.marque']), 201);
    }

    public function index(Request $request): JsonResponse
    {
        $rendezVous = RendezVous::with(['annonce.vehicule.modele.marque', 'annonce.vendeur.user'])
            ->where('acheteur_id', $request->user()->acheteur->id)
            ->orderBy('date_rdv', 'asc')
            ->orderBy('heure_rdv', 'asc')
            ->get();

        return response()->json($rendezVous);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $rendezVous = RendezVous::with(['annonce.vehicule.modele.marque', 'annonce.vendeur.user'])
            ->where('acheteur_id', $request->user()->acheteur->id)
            ->findOrFail($id);

        return response()->json($rendezVous);
    }

    public function cancel(Request $request, int $id): JsonResponse
    {
        $rendezVous = RendezVous::where('acheteur_id', $request->user()->acheteur->id)
            ->findOrFail($id);

        if ($rendezVous->statut === 'ANNULE') {
            $rendezVous->delete();
            return response()->json(['message' => 'Rendez-vous supprimé définitivement.']);
        }

        $rendezVous->update(['statut' => 'ANNULE']);

        // Libérer la disponibilité si elle existe
        if ($rendezVous->disponibilite_id) {
            $disponibilite = Disponibilite::find($rendezVous->disponibilite_id);
            if ($disponibilite) {
                $disponibilite->update(['statut' => 'LIBRE']);
            }
        }

        return response()->json(['message' => 'Rendez-vous annulé avec succès.']);
    }

    public function vendeurRendezVous(Request $request): JsonResponse
    {
        $vendeur = $request->user()->vendeur;

        $rendezVous = RendezVous::with(['annonce.vehicule.modele.marque', 'acheteur.user'])
            ->whereHas('annonce', function ($q) use ($vendeur) {
                $q->where('vendeur_id', $vendeur->id);
            })
            ->orderBy('date_rdv', 'asc')
            ->orderBy('heure_rdv', 'asc')
            ->get();

        return response()->json($rendezVous);
    }

    public function confirmer(Request $request, int $id): JsonResponse
    {
        $vendeur = $request->user()->vendeur;

        $rendezVous = RendezVous::with('annonce.vehicule.modele.marque', 'acheteur.user')
            ->whereHas('annonce', function ($q) use ($vendeur) {
                $q->where('vendeur_id', $vendeur->id);
            })->findOrFail($id);

        $rendezVous->update(['statut' => 'CONFIRME']);

        $vehiculeNom = "{$rendezVous->annonce->vehicule->modele->marque->nom} {$rendezVous->annonce->vehicule->modele->nom}";

        Notification::creer(
            $rendezVous->acheteur->user_id,
            'Rendez-vous confirmé',
            "a confirmé votre rendez-vous pour le {$vehiculeNom}.",
            'RENDEZ_VOUS',
            '/acheteur/mes-rendez-vous',
            $request->user()->id  // expediteur_id = le vendeur qui confirme
        );

        return response()->json(['message' => 'Rendez-vous confirmé.']);
    }

    public function proposerAutreDate(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'date_rdv' => 'required|date|after_or_equal:today',
            'heure_rdv' => 'required|date_format:H:i',
            'message_vendeur' => 'required|string|max:500',
        ]);

        $vendeur = $request->user()->vendeur;

        $rendezVous = RendezVous::with('annonce.vehicule.modele.marque', 'acheteur.user')
            ->whereHas('annonce', function ($q) use ($vendeur) {
                $q->where('vendeur_id', $vendeur->id);
            })->findOrFail($id);

        $rendezVous->update([
            'date_rdv' => $request->date_rdv,
            'heure_rdv' => $request->heure_rdv,
            'message_vendeur' => $request->message_vendeur,
            'statut' => 'AUTRE_DATE_PROPOSEE',
        ]);

        $vehiculeNom = "{$rendezVous->annonce->vehicule->modele->marque->nom} {$rendezVous->annonce->vehicule->modele->nom}";

        Notification::creer(
            $rendezVous->acheteur->user_id,
            'Nouvelle date proposée',
            "a proposé une nouvelle date pour votre rendez-vous concernant le {$vehiculeNom}.",
            'RENDEZ_VOUS',
            '/acheteur/mes-rendez-vous',
            $request->user()->id  // expediteur_id = le vendeur qui propose
        );

        return response()->json(['message' => 'Autre date proposée avec succès.']);
    }

    public function annulerVendeur(Request $request, int $id): JsonResponse
    {
        $vendeur = $request->user()->vendeur;

        $rendezVous = RendezVous::with('annonce.vehicule.modele.marque', 'acheteur.user')
            ->whereHas('annonce', function ($q) use ($vendeur) {
                $q->where('vendeur_id', $vendeur->id);
            })->findOrFail($id);

        $rendezVous->update(['statut' => 'ANNULE']);

        // Libérer la disponibilité si elle existe
        if ($rendezVous->disponibilite_id) {
            $disponibilite = Disponibilite::find($rendezVous->disponibilite_id);
            if ($disponibilite) {
                $disponibilite->update(['statut' => 'LIBRE']);
            }
        }

        $vehiculeNom = "{$rendezVous->annonce->vehicule->modele->marque->nom} {$rendezVous->annonce->vehicule->modele->nom}";

        Notification::creer(
            $rendezVous->acheteur->user_id,
            'Rendez-vous annulé',
            "a annulé votre rendez-vous pour le {$vehiculeNom}.",
            'RENDEZ_VOUS',
            '/acheteur/mes-rendez-vous',
            $request->user()->id  // expediteur_id = le vendeur qui annule
        );

        return response()->json(['message' => 'Rendez-vous annulé.']);
    }
}
