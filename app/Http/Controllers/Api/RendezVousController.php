<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use App\Models\RendezVous;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RendezVousController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'annonce_id' => 'required|exists:annonces,id',
            'date_rdv' => 'required|date|after_or_equal:today',
            'heure_rdv' => 'required|date_format:H:i',
            'message' => 'nullable|string|max:500',
        ]);

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

        $rendezVous = RendezVous::create([
            'acheteur_id' => $acheteurId,
            'annonce_id' => $request->annonce_id,
            'date_rdv' => $request->date_rdv,
            'heure_rdv' => $request->heure_rdv,
            'message' => $request->message,
            'statut' => 'EN_ATTENTE',
        ]);

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

        $rendezVous = RendezVous::whereHas('annonce', function ($q) use ($vendeur) {
            $q->where('vendeur_id', $vendeur->id);
        })->findOrFail($id);

        $rendezVous->update(['statut' => 'CONFIRME']);

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

        $rendezVous = RendezVous::whereHas('annonce', function ($q) use ($vendeur) {
            $q->where('vendeur_id', $vendeur->id);
        })->findOrFail($id);

        $rendezVous->update([
            'date_rdv' => $request->date_rdv,
            'heure_rdv' => $request->heure_rdv,
            'message_vendeur' => $request->message_vendeur,
            'statut' => 'AUTRE_DATE_PROPOSEE',
        ]);

        return response()->json(['message' => 'Autre date proposée avec succès.']);
    }

    public function annulerVendeur(Request $request, int $id): JsonResponse
    {
        $vendeur = $request->user()->vendeur;

        $rendezVous = RendezVous::whereHas('annonce', function ($q) use ($vendeur) {
            $q->where('vendeur_id', $vendeur->id);
        })->findOrFail($id);

        $rendezVous->update(['statut' => 'ANNULE']);

        return response()->json(['message' => 'Rendez-vous annulé.']);
    }
}
