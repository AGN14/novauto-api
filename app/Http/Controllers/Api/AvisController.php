<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Avis;
use App\Models\Annonce;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvisController extends Controller
{
    /**
     * Liste des avis approuvés d'une annonce (public)
     */
    public function index($annonceId): JsonResponse
    {
        $avis = Avis::where('annonce_id', $annonceId)
            ->where('statut', 'APPROUVE')
            ->with(['acheteur.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($avis);
    }

    /**
     * Acheteur soumet un avis
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'annonce_id' => ['required', 'exists:annonces,id'],
            'note'       => ['required', 'integer', 'between:1,5'],
            'commentaire' => ['nullable', 'string', 'max:1000'],
        ]);

        $acheteur = $request->user()->acheteur;
        $annonce = Annonce::findOrFail($request->annonce_id);

        // Vérifier que l'acheteur a visité le véhicule (réservation existante, peu importe le statut)
        $reservation = Reservation::where('acheteur_id', $acheteur->id)
            ->where('annonce_id', $request->annonce_id)
            ->first();

        if (!$reservation) {
            return response()->json([
                'message' => 'Vous devez avoir visité ce véhicule pour laisser un avis.'
            ], 403);
        }

        // Vérifier qu'il n'y a pas déjà un avis de cet acheteur pour cette annonce
        $existant = Avis::where('acheteur_id', $acheteur->id)
            ->where('annonce_id', $request->annonce_id)
            ->first();

        if ($existant) {
            return response()->json([
                'message' => 'Vous avez déjà laissé un avis pour cette annonce.'
            ], 422);
        }

        // Publier directement sans modération
        $avis = Avis::create([
            'acheteur_id' => $acheteur->id,
            'vendeur_id'  => $annonce->vendeur_id,
            'annonce_id'  => $request->annonce_id,
            'note'        => $request->note,
            'commentaire' => $request->commentaire,
            'statut'      => 'APPROUVE',
        ]);

        return response()->json([
            'message' => 'Votre avis a été publié avec succès.',
            'avis'    => $avis->load('annonce'),
        ], 201);
    }

    /**
     * Liste des avis soumis par l'acheteur connecté
     */
    public function mesAvis(Request $request): JsonResponse
    {
        $acheteur = $request->user()->acheteur;

        $avis = Avis::where('acheteur_id', $acheteur->id)
            ->with(['annonce.vehicule.modele.marque'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($avis);
    }

    /**
     * Admin : liste tous les avis pour modération
     */
    public function adminIndex(): JsonResponse
    {
        $avis = Avis::with(['acheteur.user', 'annonce.vehicule.modele.marque'])
            ->orderByRaw("FIELD(statut, 'EN_ATTENTE', 'APPROUVE', 'REJETE')")
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($avis);
    }

    /**
     * Admin : approuver un avis
     */
    public function approuver($id): JsonResponse
    {
        $avis = Avis::findOrFail($id);
        $avis->update(['statut' => 'APPROUVE']);

        return response()->json([
            'message' => 'Avis approuvé avec succès.',
            'avis'    => $avis,
        ]);
    }

    /**
     * Admin : rejeter un avis
     */
    public function rejeter($id): JsonResponse
    {
        $avis = Avis::findOrFail($id);
        $avis->update(['statut' => 'REJETE']);

        return response()->json([
            'message' => 'Avis rejeté.',
            'avis'    => $avis,
        ]);
    }

    /**
     * Vendeur : signaler un avis mensonger
     */
    public function signalerAvis(Request $request, $id): JsonResponse
    {
        $request->validate([
            'raison' => ['required', 'string', 'max:500'],
        ]);

        $avis = Avis::findOrFail($id);
        $vendeur = $request->user()->vendeur;

        // Vérifier que c'est bien le vendeur de l'annonce
        if ($avis->vendeur_id !== $vendeur->id) {
            return response()->json([
                'message' => 'Action non autorisée.'
            ], 403);
        }

        // Vérifier que l'avis n'est pas déjà signalé
        if ($avis->signale_par_vendeur) {
            return response()->json([
                'message' => 'Cet avis a déjà été signalé.'
            ], 422);
        }

        $avis->update([
            'signale_par_vendeur' => true,
            'raison_signalement' => $request->raison,
        ]);

        return response()->json([
            'message' => 'Avis signalé avec succès. L\'administrateur examinera votre demande.',
            'avis' => $avis,
        ]);
    }

    /**
     * Admin : liste des avis signalés
     */
    public function avisSignales(): JsonResponse
    {
        $avis = Avis::where('signale_par_vendeur', true)
            ->with(['acheteur.user', 'vendeur.user', 'annonce.vehicule.modele.marque'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($avis);
    }

    /**
     * Admin : supprimer un avis signalé
     */
    public function supprimerAvis($id): JsonResponse
    {
        $avis = Avis::findOrFail($id);
        $avis->delete();

        return response()->json([
            'message' => 'Avis supprimé avec succès.',
        ]);
    }

    /**
     * Admin : rejeter le signalement (garder l'avis)
     */
    public function rejeterSignalement($id): JsonResponse
    {
        $avis = Avis::findOrFail($id);
        $avis->update([
            'signale_par_vendeur' => false,
            'raison_signalement' => null,
        ]);

        return response()->json([
            'message' => 'Signalement rejeté. L\'avis reste publié.',
            'avis' => $avis,
        ]);
    }
}
