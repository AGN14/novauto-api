<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Annonce\CreateAnnonceRequest;
use App\Http\Requests\Annonce\UpdateAnnonceRequest;
use App\Models\Annonce;
use App\Services\AnnonceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnnonceController extends Controller
{
    public function __construct(private AnnonceService $annonceService) {}

    public function index(Request $request): JsonResponse
    {
        $query = Annonce::with(['vehicule.modele.marque'])
            ->where('statut', 'DISPONIBLE');

        // Filtre par nom de marque (depuis carrousel)
        if ($request->has('marque') && $request->marque) {
            $query->whereHas('vehicule.modele.marque', function ($q) use ($request) {
                $q->where('nom', 'LIKE', '%' . $request->marque . '%');
            });
        }

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

    public function mesAnnonces(Request $request): JsonResponse
    {
        $vendeur = $request->user()->vendeur;

        if (!$vendeur) {
            return response()->json(['message' => 'Compte vendeur introuvable.'], 403);
        }

        $annonces = $this->annonceService->getMesAnnonces($vendeur->id);
        return response()->json($annonces);
    }

    public function store(CreateAnnonceRequest $request): JsonResponse
    {
        $vendeur = $request->user()->vendeur;

        if (!$vendeur) {
            return response()->json(['message' => 'Compte vendeur introuvable.'], 403);
        }

        try {
            $annonce = $this->annonceService->create($request->validated(), $vendeur->id);
            return response()->json($annonce, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(UpdateAnnonceRequest $request, int $id): JsonResponse
    {
        $annonce = Annonce::findOrFail($id);
        $vendeur = $request->user()->vendeur;

        if ($annonce->vendeur_id !== $vendeur->id) {
            return response()->json(['message' => 'Action non autorisée.'], 403);
        }

        try {
            $annonce = $this->annonceService->update($annonce, $request->validated());
            return response()->json($annonce);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $annonce = Annonce::findOrFail($id);
        $vendeur = $request->user()->vendeur;

        if ($annonce->vendeur_id !== $vendeur->id) {
            return response()->json(['message' => 'Action non autorisée.'], 403);
        }

        $this->annonceService->delete($annonce);
        return response()->json(['message' => 'Annonce supprimée avec succès.']);
    }
}