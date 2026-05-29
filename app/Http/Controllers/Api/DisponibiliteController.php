<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Disponibilite;
use App\Models\DisponibiliteGarage;
use App\Models\Annonce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DisponibiliteController extends Controller
{
    /**
     * Liste des disponibilités du vendeur connecté
     */
    public function vendeurDisponibilites(Request $request)
    {
        $user = $request->user();
        $vendeur = $user->vendeur;

        if (!$vendeur) {
            return response()->json(['message' => 'Vendeur non trouvé'], 404);
        }

        $disponibilites = Disponibilite::where('vendeur_id', $vendeur->id)
            ->orderBy('jour')
            ->orderBy('heure_debut')
            ->get();

        return response()->json($disponibilites);
    }

    /**
     * Créer une nouvelle disponibilité (vendeur)
     */
    public function creerDisponibilite(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jour' => 'required|date|after_or_equal:today',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $vendeur = $user->vendeur;

        if (!$vendeur) {
            return response()->json(['message' => 'Vendeur non trouvé'], 404);
        }

        // Vérifier conflit avec créneaux existants
        $conflit = Disponibilite::where('vendeur_id', $vendeur->id)
            ->where('jour', $request->jour)
            ->where(function ($query) use ($request) {
                $query->whereBetween('heure_debut', [$request->heure_debut, $request->heure_fin])
                      ->orWhereBetween('heure_fin', [$request->heure_debut, $request->heure_fin])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('heure_debut', '<=', $request->heure_debut)
                            ->where('heure_fin', '>=', $request->heure_fin);
                      });
            })
            ->exists();

        if ($conflit) {
            return response()->json([
                'message' => 'Ce créneau chevauche une disponibilité existante'
            ], 422);
        }

        $disponibilite = Disponibilite::create([
            'vendeur_id' => $vendeur->id,
            'jour' => $request->jour,
            'heure_debut' => $request->heure_debut,
            'heure_fin' => $request->heure_fin,
            'statut' => 'LIBRE'
        ]);

        return response()->json($disponibilite, 201);
    }

    /**
     * Supprimer une disponibilité (si LIBRE)
     */
    public function supprimerDisponibilite(Request $request, int $id)
    {
        $user = $request->user();
        $vendeur = $user->vendeur;

        if (!$vendeur) {
            return response()->json(['message' => 'Vendeur non trouvé'], 404);
        }

        $disponibilite = Disponibilite::where('id', $id)
            ->where('vendeur_id', $vendeur->id)
            ->first();

        if (!$disponibilite) {
            return response()->json(['message' => 'Disponibilité non trouvée'], 404);
        }

        if ($disponibilite->statut === 'OCCUPE') {
            return response()->json([
                'message' => 'Impossible de supprimer un créneau occupé'
            ], 422);
        }

        $disponibilite->delete();

        return response()->json(['message' => 'Disponibilité supprimée avec succès']);
    }

    /**
     * Disponibilités LIBRES d'un vendeur pour une annonce (public)
     */
    public function disponibilitesParAnnonce(Request $request, int $annonceId)
    {
        $annonce = Annonce::findOrFail($annonceId);

        $disponibilites = Disponibilite::where('vendeur_id', $annonce->vendeur_id)
            ->where('statut', 'LIBRE')
            ->where('jour', '>=', Carbon::today())
            ->orderBy('jour')
            ->orderBy('heure_debut')
            ->get();

        return response()->json($disponibilites);
    }

    /**
     * Marquer un créneau comme OCCUPE
     */
    public function occuperCreneau(int $id)
    {
        $disponibilite = Disponibilite::findOrFail($id);
        $disponibilite->update(['statut' => 'OCCUPE']);

        return response()->json(['message' => 'Créneau marqué comme occupé']);
    }

    /**
     * Marquer un créneau comme LIBRE
     */
    public function libererCreneau(int $id)
    {
        $disponibilite = Disponibilite::findOrFail($id);
        $disponibilite->update(['statut' => 'LIBRE']);

        return response()->json(['message' => 'Créneau libéré']);
    }

    // ===== GARAGE =====

    /**
     * Liste des disponibilités du garage connecté
     */
    public function garageDisponibilites(Request $request)
    {
        $garage = $request->user();

        $disponibilites = DisponibiliteGarage::where('garage_id', $garage->id)
            ->orderBy('jour')
            ->orderBy('heure_debut')
            ->get();

        return response()->json($disponibilites);
    }

    /**
     * Créer une disponibilité (garage)
     */
    public function creerDisponibiliteGarage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jour' => 'required|date|after_or_equal:today',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $garage = $request->user();

        // Vérifier conflit
        $conflit = DisponibiliteGarage::where('garage_id', $garage->id)
            ->where('jour', $request->jour)
            ->where(function ($query) use ($request) {
                $query->whereBetween('heure_debut', [$request->heure_debut, $request->heure_fin])
                      ->orWhereBetween('heure_fin', [$request->heure_debut, $request->heure_fin])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('heure_debut', '<=', $request->heure_debut)
                            ->where('heure_fin', '>=', $request->heure_fin);
                      });
            })
            ->exists();

        if ($conflit) {
            return response()->json([
                'message' => 'Ce créneau chevauche une disponibilité existante'
            ], 422);
        }

        $disponibilite = DisponibiliteGarage::create([
            'garage_id' => $garage->id,
            'jour' => $request->jour,
            'heure_debut' => $request->heure_debut,
            'heure_fin' => $request->heure_fin,
            'statut' => 'LIBRE'
        ]);

        return response()->json($disponibilite, 201);
    }

    /**
     * Disponibilités LIBRES d'un garage (pour vendeur)
     */
    public function disponibilitesGaragePublic(int $garageId)
    {
        $disponibilites = DisponibiliteGarage::where('garage_id', $garageId)
            ->where('statut', 'LIBRE')
            ->where('jour', '>=', Carbon::today())
            ->orderBy('jour')
            ->orderBy('heure_debut')
            ->get();

        return response()->json($disponibilites);
    }
}
