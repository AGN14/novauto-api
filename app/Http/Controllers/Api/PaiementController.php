<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\Reservation;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use FedaPay\FedaPay;
use FedaPay\Transaction;

class PaiementController extends Controller
{
    public function __construct()
    {
        FedaPay::setApiKey(config('services.fedapay.secret_key'));
        FedaPay::setEnvironment(config('services.fedapay.environment'));
    }

    public function initier(Request $request): JsonResponse
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
        ]);

        $reservation = Reservation::with('annonce.vehicule.modele.marque')->findOrFail($request->reservation_id);

        if ($reservation->acheteur_id !== $request->user()->acheteur->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        if ($reservation->statut === 'CONFIRMEE') {
            return response()->json(['message' => 'Cette réservation est déjà payée'], 422);
        }

        $existingPaiement = Paiement::where('reservation_id', $reservation->id)
            ->where('statut', 'APPROUVE')
            ->first();

        if ($existingPaiement) {
            return response()->json(['message' => 'Paiement déjà effectué pour cette réservation'], 422);
        }

        $paiement = Paiement::create([
            'reservation_id' => $reservation->id,
            'montant' => $reservation->montant_paye,
            'statut' => 'EN_ATTENTE',
        ]);

        try {
            $vehiculeNom = "{$reservation->annonce->vehicule->modele->marque->nom} {$reservation->annonce->vehicule->modele->nom}";

            $transaction = Transaction::create([
                'description' => "Réservation véhicule NOVAuto - {$vehiculeNom}",
                'amount' => (int)($reservation->montant_paye),
                'currency' => ['iso' => 'XOF'],
                'callback_url' => config('app.url') . '/api/paiements/callback',
                'return_url' => config('services.frontend_url', 'http://localhost:4200') . '/acheteur/paiement-retour?reservation_id=' . $reservation->id,
                'customer' => [
                    'firstname' => $request->user()->prenom,
                    'lastname' => $request->user()->nom,
                    'email' => $request->user()->email,
                    'phone_number' => [
                        'number' => $request->user()->telephone ?? '00000000',
                        'country' => 'BJ'
                    ],
                ],
            ]);

            $token = $transaction->generateToken();

            $paiement->update([
                'transaction_id' => $transaction->id,
                'reference' => $transaction->reference,
            ]);

            return response()->json([
                'payment_url' => $token->url,
                'transaction_id' => $transaction->id,
                'reference' => $transaction->reference,
            ]);

        } catch (\Exception $e) {
            \Log::error('FedaPay initiation error', [
                'error' => $e->getMessage(),
                'reservation_id' => $reservation->id,
            ]);

            $paiement->update(['statut' => 'ECHOUE']);

            return response()->json([
                'message' => 'Erreur lors de l\'initialisation du paiement',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function callback(Request $request): JsonResponse
    {
        \Log::info('FedaPay callback received', $request->all());

        $transactionId = $request->input('id');

        if (!$transactionId) {
            return response()->json(['message' => 'ID de transaction manquant'], 400);
        }

        try {
            $transaction = Transaction::retrieve($transactionId);

            $paiement = Paiement::where('transaction_id', $transactionId)->first();

            if (!$paiement) {
                \Log::error('Paiement not found for transaction', ['transaction_id' => $transactionId]);
                return response()->json(['message' => 'Paiement introuvable'], 404);
            }

            $reservation = Reservation::with('annonce.vehicule.modele.marque', 'annonce.vendeur.user', 'acheteur.user')
                ->findOrFail($paiement->reservation_id);

            if ($transaction->status === 'approved') {
                $paiement->update(['statut' => 'APPROUVE']);

                $reservation->update(['statut' => 'CONFIRMEE']);

                $vehiculeNom = "{$reservation->annonce->vehicule->modele->marque->nom} {$reservation->annonce->vehicule->modele->nom}";

                Notification::creer(
                    $reservation->acheteur->user_id,
                    'Paiement confirmé',
                    "Votre paiement pour le {$vehiculeNom} a été confirmé.",
                    'PAIEMENT',
                    '/acheteur/mes-reservations'
                );

                Notification::creer(
                    $reservation->annonce->vendeur->user_id,
                    'Nouvelle réservation payée',
                    "Une réservation payée pour votre {$vehiculeNom} a été reçue.",
                    'RESERVATION',
                    '/vendeur/reservations'
                );

                \Log::info('Payment approved', [
                    'transaction_id' => $transactionId,
                    'reservation_id' => $reservation->id,
                ]);

            } elseif ($transaction->status === 'declined' || $transaction->status === 'canceled') {
                $paiement->update(['statut' => 'ECHOUE']);

                \Log::info('Payment declined/canceled', [
                    'transaction_id' => $transactionId,
                    'status' => $transaction->status,
                ]);
            }

            return response()->json(['message' => 'Callback traité'], 200);

        } catch (\Exception $e) {
            \Log::error('FedaPay callback error', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId,
            ]);

            return response()->json([
                'message' => 'Erreur lors du traitement du callback',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function verifier(Request $request, int $reservationId): JsonResponse
    {
        $reservation = Reservation::with('annonce.vehicule.modele.marque')
            ->where('acheteur_id', $request->user()->acheteur->id)
            ->findOrFail($reservationId);

        $paiement = Paiement::where('reservation_id', $reservationId)->latest()->first();

        if (!$paiement) {
            return response()->json([
                'statut' => 'NON_PAYE',
                'message' => 'Aucun paiement trouvé',
            ]);
        }

        try {
            if ($paiement->transaction_id) {
                $transaction = Transaction::retrieve($paiement->transaction_id);

                if ($transaction->status === 'approved' && $paiement->statut !== 'APPROUVE') {
                    $paiement->update(['statut' => 'APPROUVE']);
                    $reservation->update(['statut' => 'CONFIRMEE']);

                    $vehiculeNom = "{$reservation->annonce->vehicule->modele->marque->nom} {$reservation->annonce->vehicule->modele->nom}";

                    Notification::creer(
                        $reservation->acheteur->user_id,
                        'Paiement confirmé',
                        "Votre paiement pour le {$vehiculeNom} a été confirmé.",
                        'PAIEMENT',
                        '/acheteur/mes-reservations'
                    );

                    Notification::creer(
                        $reservation->annonce->vendeur->user_id,
                        'Nouvelle réservation payée',
                        "Une réservation payée pour votre {$vehiculeNom} a été reçue.",
                        'RESERVATION',
                        '/vendeur/reservations'
                    );
                }

                return response()->json([
                    'statut' => $paiement->statut,
                    'transaction_status' => $transaction->status,
                    'reference' => $paiement->reference,
                ]);
            }

            return response()->json([
                'statut' => $paiement->statut,
                'reference' => $paiement->reference,
            ]);

        } catch (\Exception $e) {
            \Log::error('FedaPay verification error', [
                'error' => $e->getMessage(),
                'reservation_id' => $reservationId,
            ]);

            return response()->json([
                'statut' => $paiement->statut,
                'message' => 'Impossible de vérifier le statut avec FedaPay',
            ]);
        }
    }
}
