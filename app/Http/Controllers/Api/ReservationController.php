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

        if ($annonce->statut !== 'DISPONIBLE') {
            return response()->json(['message' => 'Ce véhicule n\'est plus disponible.'], 422);
        }

        $reservation = Reservation::create([
            'acheteur_id'      => $request->user()->acheteur->id,
            'annonce_id'       => $request->annonce_id,
            'montant_acompte'  => $request->montant,
            'statut'           => 'EN_ATTENTE',
            'date_reservation' => now(),
            'date_expiration'  => now()->addHours(48),
        ]);

        $annonce->update(['statut' => 'RESERVEE']);

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

        if ($reservation->statut === 'CONFIRMEE') {
            return response()->json(['message' => 'Impossible d\'annuler une réservation confirmée.'], 422);
        }

        $reservation->update(['statut' => 'ANNULEE']);
        $reservation->annonce->update(['statut' => 'DISPONIBLE']);

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
}