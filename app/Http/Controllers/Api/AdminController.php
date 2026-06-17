<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use App\Models\User;
use App\Models\Vendeur;
use App\Models\GaragePartenaire;
use App\Mail\GarageApprouveMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

    public function garages(Request $request): JsonResponse
    {
        $garages = GaragePartenaire::orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));
        return response()->json($garages);
    }

    public function certifierGarage(int $id): JsonResponse
    {
        $garage = GaragePartenaire::findOrFail($id);
        $garage->update([
            'certifie'           => true,
            'date_certification' => now(),
        ]);
        return response()->json([
            'message' => 'Garage certifié avec succès.',
            'garage'  => $garage,
        ]);
    }

    public function suspendreGarage(int $id): JsonResponse
    {
        $garage = GaragePartenaire::findOrFail($id);
        $garage->update([
            'certifie'           => false,
            'date_certification' => null,
        ]);
        return response()->json([
            'message' => 'Certification du garage révoquée.',
            'garage'  => $garage,
        ]);
    }

    public function approuverGarage(int $id): JsonResponse
    {
        $garage = GaragePartenaire::findOrFail($id);

        $garage->update([
            'agree'           => true,
            'statut_demande'  => 'APPROUVEE',
            'date_agrement'   => now(),
            'message_demande' => null,
        ]);

        // Envoyer email de confirmation
        try {
            Mail::to($garage->email)->send(new GarageApprouveMail($garage->fresh()));
        } catch (\Exception $e) {
            \Log::error('Erreur envoi email garage approuvé: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Demande du garage approuvée. Le garage peut maintenant se connecter.',
            'garage'  => $garage->fresh(),
        ]);
    }

    public function rejeterGarage(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'motif' => ['nullable', 'string', 'max:500'],
        ]);

        $garage = GaragePartenaire::findOrFail($id);

        $garage->update([
            'agree'           => false,
            'statut_demande'  => 'REJETEE',
            'message_demande' => $request->motif,
        ]);

        return response()->json([
            'message' => 'Demande du garage rejetée.',
            'garage'  => $garage,
        ]);
    }
}