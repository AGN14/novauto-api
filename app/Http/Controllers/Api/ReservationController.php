<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use App\Models\RendezVous;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'annonce_id' => 'required|exists:annonces,id',
        ]);

        $annonce = Annonce::findOrFail($request->annonce_id);
        $acheteurId = $request->user()->acheteur->id;

        if ($annonce->statut === 'RESERVEE' || $annonce->statut === 'VENDUE') {
            return response()->json(['message' => 'Ce véhicule n\'est plus disponible.'], 422);
        }

        $existingReservation = Reservation::where('acheteur_id', $acheteurId)
            ->where('annonce_id', $request->annonce_id)
            ->whereIn('statut', ['EN_ATTENTE', 'CONFIRMEE'])
            ->first();

        if ($existingReservation) {
            return response()->json(['message' => 'Vous avez déjà une réservation pour ce véhicule.'], 422);
        }

        $existingRdv = RendezVous::where('acheteur_id', $acheteurId)
            ->where('annonce_id', $request->annonce_id)
            ->whereIn('statut', ['EN_ATTENTE', 'CONFIRME', 'AUTRE_DATE_PROPOSEE'])
            ->first();

        if ($existingRdv) {
            return response()->json(['message' => 'Vous avez déjà un rendez-vous pour ce véhicule.'], 422);
        }

        $montantReservation = $annonce->montant_reservation ?? ($annonce->prix * 0.10);

        $reservation = Reservation::create([
            'acheteur_id' => $acheteurId,
            'annonce_id' => $request->annonce_id,
            'montant_paye' => $montantReservation,
            'statut' => 'EN_ATTENTE',
        ]);

        $annonce->update(['statut' => 'RESERVEE']);

        return response()->json($reservation->load(['annonce.vehicule.modele.marque']), 201);
    }

    public function index(Request $request): JsonResponse
    {
        $reservations = Reservation::with(['annonce.vehicule.modele.marque', 'annonce.vendeur.user'])
            ->where('acheteur_id', $request->user()->acheteur->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($reservations);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $reservation = Reservation::with(['annonce.vehicule.modele.marque', 'annonce.vendeur.user'])
            ->where('acheteur_id', $request->user()->acheteur->id)
            ->findOrFail($id);

        return response()->json($reservation);
    }

    public function cancel(Request $request, int $id): JsonResponse
    {
        $reservation = Reservation::where('acheteur_id', $request->user()->acheteur->id)
            ->findOrFail($id);

        if ($reservation->statut === 'ANNULEE') {
            $reservation->delete();
            return response()->json(['message' => 'Réservation supprimée définitivement.']);
        }

        $reservation->update(['statut' => 'ANNULEE']);

        if ($reservation->annonce->statut === 'RESERVEE') {
            $reservation->annonce->update(['statut' => 'DISPONIBLE']);
        }

        return response()->json(['message' => 'Réservation annulée avec succès.']);
    }

    public function vendeurReservations(Request $request): JsonResponse
    {
        $vendeur = $request->user()->vendeur;

        $reservations = Reservation::with(['annonce.vehicule.modele.marque', 'acheteur.user'])
            ->whereHas('annonce', function ($q) use ($vendeur) {
                $q->where('vendeur_id', $vendeur->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($reservations);
    }

    public function confirmer(Request $request, int $id): JsonResponse
    {
        $vendeur = $request->user()->vendeur;

        $reservation = Reservation::whereHas('annonce', function ($q) use ($vendeur) {
            $q->where('vendeur_id', $vendeur->id);
        })->findOrFail($id);

        $reservation->update(['statut' => 'CONFIRMEE']);

        return response()->json(['message' => 'Réservation confirmée.']);
    }

    public function annulerVendeur(Request $request, int $id): JsonResponse
    {
        $vendeur = $request->user()->vendeur;

        $reservation = Reservation::whereHas('annonce', function ($q) use ($vendeur) {
            $q->where('vendeur_id', $vendeur->id);
        })->findOrFail($id);

        $reservation->update(['statut' => 'ANNULEE']);

        if ($reservation->annonce->statut === 'RESERVEE') {
            $reservation->annonce->update(['statut' => 'DISPONIBLE']);
        }

        return response()->json(['message' => 'Réservation annulée.']);
    }
}