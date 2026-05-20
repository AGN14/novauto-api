<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnnonceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Annonce::with(['vehicule.modele.marque'])
            ->where('statut', 'DISPONIBLE');

        if ($request->has('marque_id') && $request->marque_id) {
            $query->whereHas('vehicule.modele', function ($q) use ($request) {
                $q->where('marque_id', $request->marque_id);
            });
        }

        if ($request->has('modele_id') && $request->modele_id) {
            $query->whereHas('vehicule', function ($q) use ($request) {
                $q->where('modele_id', $request->modele_id);
            });
        }

        if ($request->has('statut_douanier') && $request->statut_douanier) {
            $query->whereHas('vehicule', function ($q) use ($request) {
                $q->where('statut_douanier', $request->statut_douanier);
            });
        }

        if ($request->has('prix_max') && $request->prix_max) {
            $query->where('prix', '<=', $request->prix_max);
        }

        if ($request->has('prix_min') && $request->prix_min) {
            $query->where('prix', '>=', $request->prix_min);
        }

        if ($request->has('vin_verifie') && $request->vin_verifie) {
            $query->whereHas('vehicule', function ($q) {
                $q->where('vin_verifie', true);
            });
        }

        $annonces = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 12));

        return response()->json($annonces);
    }

    public function show(int $id): JsonResponse
    {
        $annonce = Annonce::with([
            'vehicule.modele.marque',
            'vendeur.user',
        ])->findOrFail($id);

        return response()->json($annonce);
    }

    public function featured(): JsonResponse
    {
        $annonces = Annonce::with(['vehicule.modele.marque'])
            ->where('statut', 'DISPONIBLE')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        return response()->json($annonces);
    }
}