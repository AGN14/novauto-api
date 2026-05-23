<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GaragePartenaire;
use App\Models\RapportInspection;
use App\Models\Annonce;
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
            'statut'          => 'EN_ATTENTE',
            'date_soumission' => now(),
        ]);

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
}