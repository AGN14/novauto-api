<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use App\Models\User;
use App\Models\Vendeur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function stats(): JsonResponse
    {
        return response()->json([
            'totalVendeurs'       => Vendeur::count(),
            'totalAcheteurs'      => User::where('role', 'ACHETEUR')->count(),
            'totalAnnonces'       => Annonce::count(),
            'annoncesDisponibles' => Annonce::where('statut', 'DISPONIBLE')->count(),
            'annoncesReservees'   => Annonce::where('statut', 'RESERVEE')->count(),
            'annoncesVendues'     => Annonce::where('statut', 'VENDUE')->count(),
        ]);
    }

    public function annonces(Request $request): JsonResponse
    {
        $annonces = Annonce::with(['vehicule.modele.marque', 'vendeur.user'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($annonces);
    }

    public function vendeurs(Request $request): JsonResponse
    {
        $vendeurs = Vendeur::with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($vendeurs);
    }

    public function updateAnnonce(Request $request, int $id): JsonResponse
    {
        $annonce = Annonce::findOrFail($id);
        $annonce->update($request->only(['statut']));
        return response()->json($annonce);
    }

    public function deleteAnnonce(int $id): JsonResponse
    {
        Annonce::findOrFail($id)->delete();
        return response()->json(['message' => 'Annonce supprimée.']);
    }

    public function certifierVendeur(int $id): JsonResponse
    {
        $vendeur = Vendeur::findOrFail($id);
        $vendeur->update(['certifie' => true]);
        return response()->json(['message' => 'Vendeur certifié avec succès.']);
    }

    public function suspendreVendeur(int $id): JsonResponse
    {
        $vendeur = Vendeur::findOrFail($id);
        $vendeur->update(['certifie' => false]);
        return response()->json(['message' => 'Vendeur suspendu.']);
    }
}