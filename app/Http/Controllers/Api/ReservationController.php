<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reservation\CreateReservationRequest;
use App\Models\Annonce;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $reservations = Reservation::with(['annonce.vehicule.modele.marque', 'annonce.vendeur.user'])
            ->where('acheteur_id', $request->user()->acheteur->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($reservations);
    }

    public function store(CreateReservationRequest $request): JsonResponse
    {
        $annonce = Annonce::findOrFail($request->annonce_id);

        if ($annonce->statut === 'RESERVEE') {
            return response()->json(['message' => 'Ce véhicule est déjà réservé.'], 422);
        }

        $typeReservation = $request->type_reservation;

        $reservation = Reservation::create([
            'acheteur_id'      => $request->user()->acheteur->id,
            'annonce_id'       => $request->annonce_id,
            'montant_acompte'  => $typeReservation === 'ACOMPTE' ? $request->montant : 0,
            'statut'           => 'EN_ATTENTE',
            'date_reservation' => now(),
            'date_expiration'  => now()->addHours(48),
        ]);

        // L'annonce reste DISPONIBLE — elle passera en RESERVEE uniquement
        // après confirmation d'une réservation avec acompte

        return response()->json($reservation->load(['annonce.vehicule.modele.marque']), 201);
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

        // Bloquer l'annulation si acompte CONFIRMEE
        if ($reservation->statut === 'CONFIRMEE' && $reservation->montant_acompte > 0) {
            return response()->json(['message' => 'Impossible d\'annuler une réservation confirmée avec acompte.'], 422);
        }

        // Permettre d'annuler une visite (montant_acompte == 0) librement
        // Permettre d'annuler un acompte EN_ATTENTE (pas encore confirmé)

        // Si déjà ANNULEE, c'est une suppression définitive
        if ($reservation->statut === 'ANNULEE') {
            $reservation->delete();
            return response()->json(['message' => 'Réservation supprimée définitivement.']);
        }

        // Sinon, mettre le statut à ANNULEE
        $reservation->update(['statut' => 'ANNULEE']);

        // Remettre l'annonce en DISPONIBLE si elle était RESERVEE
        if ($reservation->annonce->statut === 'RESERVEE') {
            $reservation->annonce->update(['statut' => 'DISPONIBLE']);
        }

        return response()->json(['message' => 'Réservation annulée avec succès.']);
    }

    public function convertirEnAcompte(Request $request, int $id): JsonResponse
    {
        $reservation = Reservation::where('acheteur_id', $request->user()->acheteur->id)
            ->findOrFail($id);

        // Vérifier que montant_acompte == 0 (c'est bien une visite)
        if ($reservation->montant_acompte > 0) {
            return response()->json(['message' => 'Cette réservation a déjà un acompte.'], 422);
        }

        // Vérifier que le statut est EN_ATTENTE ou CONFIRMEE
        if (!in_array($reservation->statut, ['EN_ATTENTE', 'CONFIRMEE'])) {
            return response()->json(['message' => 'Cette réservation ne peut pas être convertie.'], 422);
        }

        // Calculer 10% du prix de l'annonce
        $montantAcompte = $reservation->annonce->prix * 0.10;

        // Mettre à jour : montant_acompte = 10% du prix, statut = EN_ATTENTE
        $reservation->update([
            'montant_acompte' => $montantAcompte,
            'statut' => 'EN_ATTENTE',
        ]);

        // Mettre à jour le statut de l'annonce : RESERVEE
        $reservation->annonce->update(['statut' => 'RESERVEE']);

        return response()->json([
            'message' => 'Visite convertie en réservation avec acompte.',
            'reservation' => $reservation->load(['annonce.vehicule.modele.marque']),
        ]);
    }

    public function convertirEnVisite(Request $request, int $id): JsonResponse
    {
        $reservation = Reservation::where('acheteur_id', $request->user()->acheteur->id)
            ->findOrFail($id);

        if ($reservation->montant_acompte == 0) {
            return response()->json(['message' => 'Cette réservation est déjà une visite.'], 422);
        }

        if ($reservation->statut === 'CONFIRMEE') {
            return response()->json(['message' => 'Impossible de convertir une réservation confirmée.'], 422);
        }

        $reservation->update(['montant_acompte' => 0]);
        $reservation->annonce->update(['statut' => 'DISPONIBLE']);

        return response()->json([
            'message' => 'Réservation convertie en visite.',
            'reservation' => $reservation->load(['annonce.vehicule.modele.marque']),
        ]);
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

        // Bloquer l'annonce UNIQUEMENT si la réservation avait un acompte
        if ($reservation->montant_acompte > 0) {
            $reservation->annonce->update(['statut' => 'RESERVEE']);
        }

        return response()->json(['message' => 'Réservation confirmée.']);
    }
}