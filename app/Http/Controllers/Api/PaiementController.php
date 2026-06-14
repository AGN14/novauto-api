<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\Reservation;
use App\Models\RapportInspection;
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

    // ===== PAIEMENT RÉSERVATION =====

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
            'type'           => 'RESERVATION',
            'montant'        => $reservation->montant_paye,
            'statut'         => 'EN_ATTENTE',
        ]);

        try {
            $vehiculeNom = "{$reservation->annonce->vehicule->modele->marque->nom} {$reservation->annonce->vehicule->modele->nom}";
            $returnUrl = config('services.frontend_url', 'http://localhost:4200') . '/acheteur/paiement-retour?reservation_id=' . $reservation->id;

            $transaction = Transaction::create([
                'description' => "Réservation véhicule NOVAuto - {$vehiculeNom}",
                'amount'      => (int)($reservation->montant_paye),
                'currency'    => ['iso' => 'XOF'],
                'callback_url' => config('app.url') . '/api/paiements/callback',
                'return_url'   => $returnUrl,
                'cancel_url'   => $returnUrl,
                'customer'     => [
                    'firstname' => $request->user()->prenom,
                    'lastname'  => $request->user()->nom,
                    'email'     => $request->user()->email,
                    'phone_number' => [
                        'number'  => $request->user()->telephone ?? '00000000',
                        'country' => 'BJ'
                    ],
                ],
            ]);

            $token = $transaction->generateToken();

            $paiement->update([
                'transaction_id' => $transaction->id,
                'reference'      => $transaction->reference,
            ]);

            return response()->json([
                'payment_url'    => $token->url,
                'transaction_id' => $transaction->id,
                'reference'      => $transaction->reference,
            ]);

        } catch (\Exception $e) {
            \Log::error('FedaPay initiation error', ['error' => $e->getMessage()]);
            $paiement->update(['statut' => 'ECHOUE']);
            return response()->json(['message' => 'Erreur lors de l\'initialisation du paiement', 'error' => $e->getMessage()], 500);
        }
    }

    // ===== PAIEMENT INSPECTION =====

    public function initierInspection(Request $request): JsonResponse
    {
        $request->validate([
            'rapport_id' => 'required|exists:rapports_inspection,id',
        ]);

        $vendeur = $request->user()->vendeur;

        $rapport = RapportInspection::with([
            'garage',
            'annonce.vehicule.modele.marque',
            'annonce.vendeur.user'
        ])->findOrFail($request->rapport_id);

        // Vérifier que c'est bien le vendeur de l'annonce
        if ($rapport->annonce->vendeur_id !== $vendeur->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        // Vérifier que l'inspection est en attente
        if ($rapport->statut !== 'EN_ATTENTE') {
            return response()->json(['message' => 'Cette inspection ne peut plus être payée'], 422);
        }

        // Vérifier qu'il n'y a pas déjà un paiement approuvé
        $existingPaiement = Paiement::where('rapport_inspection_id', $rapport->id)
            ->where('statut', 'APPROUVE')
            ->first();

        if ($existingPaiement) {
            return response()->json(['message' => 'Cette inspection a déjà été payée'], 422);
        }

        $montant = $rapport->garage->prix_inspection;

        $paiement = Paiement::create([
            'rapport_inspection_id' => $rapport->id,
            'type'                  => 'INSPECTION',
            'montant'               => $montant,
            'statut'                => 'EN_ATTENTE',
        ]);

        try {
            $vehiculeNom = "{$rapport->annonce->vehicule->modele->marque->nom} {$rapport->annonce->vehicule->modele->nom}";
            $returnUrl = config('services.frontend_url', 'http://localhost:4200')
                . '/vendeur/paiement-inspection-retour?rapport_id=' . $rapport->id;

            $transaction = Transaction::create([
                'description'  => "Inspection véhicule NOVAuto - {$vehiculeNom} par {$rapport->garage->nom}",
                'amount'       => (int)$montant,
                'currency'     => ['iso' => 'XOF'],
                'callback_url' => config('app.url') . '/api/paiements/callback',
                'return_url'   => $returnUrl,
                'cancel_url'   => $returnUrl,
                'customer'     => [
                    'firstname' => $request->user()->prenom,
                    'lastname'  => $request->user()->nom,
                    'email'     => $request->user()->email,
                    'phone_number' => [
                        'number'  => $request->user()->telephone ?? '00000000',
                        'country' => 'BJ'
                    ],
                ],
            ]);

            $token = $transaction->generateToken();

            $paiement->update([
                'transaction_id' => $transaction->id,
                'reference'      => $transaction->reference,
            ]);

            return response()->json([
                'payment_url'    => $token->url,
                'transaction_id' => $transaction->id,
                'reference'      => $transaction->reference,
                'montant'        => $montant,
            ]);

        } catch (\Exception $e) {
            \Log::error('FedaPay inspection initiation error', ['error' => $e->getMessage()]);
            $paiement->update(['statut' => 'ECHOUE']);
            return response()->json(['message' => 'Erreur lors de l\'initialisation du paiement', 'error' => $e->getMessage()], 500);
        }
    }

    // ===== CALLBACK FEDAPAY =====

    public function callback(Request $request)
    {
        \Log::info('FedaPay callback received', $request->all());

        $transactionId = $request->input('id');
        $isUserRedirect = $request->has('status');

        if (!$transactionId) {
            if ($isUserRedirect) {
                return redirect(config('services.frontend_url') . '/acheteur/mes-reservations');
            }
            return response()->json(['message' => 'ID de transaction manquant'], 400);
        }

        try {
            $transaction = Transaction::retrieve($transactionId);
            $paiement = Paiement::where('transaction_id', $transactionId)->first();

            if (!$paiement) {
                \Log::error('Paiement not found for transaction', ['transaction_id' => $transactionId]);
                if ($isUserRedirect) {
                    return redirect(config('services.frontend_url') . '/acheteur/mes-reservations');
                }
                return response()->json(['message' => 'Paiement introuvable'], 404);
            }

            // ===== PAIEMENT RÉSERVATION =====
            if ($paiement->type === 'RESERVATION') {
                $reservation = Reservation::with('annonce.vehicule.modele.marque', 'annonce.vendeur.user', 'acheteur.user')
                    ->findOrFail($paiement->reservation_id);

                if ($transaction->status === 'approved') {
                    $paiement->update(['statut' => 'APPROUVE']);
                    $reservation->update(['statut' => 'CONFIRMEE']);

                    $vehiculeNom = "{$reservation->annonce->vehicule->modele->marque->nom} {$reservation->annonce->vehicule->modele->nom}";

                    Notification::creer($reservation->acheteur->user_id, 'Paiement confirmé',
                        "Votre paiement pour le {$vehiculeNom} a été confirmé.", 'PAIEMENT', '/acheteur/mes-reservations');

                    Notification::creer($reservation->annonce->vendeur->user_id, 'Nouvelle réservation payée',
                        "Une réservation payée pour votre {$vehiculeNom} a été reçue.", 'RESERVATION', '/vendeur/reservations');

                } elseif (in_array($transaction->status, ['declined', 'canceled'])) {
                    $paiement->update(['statut' => 'ECHOUE']);
                }

                if ($isUserRedirect) {
                    return redirect(config('services.frontend_url') . '/acheteur/paiement-retour?reservation_id=' . $reservation->id);
                }
            }

            // ===== PAIEMENT INSPECTION =====
            if ($paiement->type === 'INSPECTION') {
                $rapport = RapportInspection::with('annonce.vendeur.user', 'garage')
                    ->findOrFail($paiement->rapport_inspection_id);

                if ($transaction->status === 'approved') {
                    $paiement->update(['statut' => 'APPROUVE']);

                    Notification::creer($rapport->annonce->vendeur->user_id, 'Paiement inspection confirmé',
                        "Votre paiement d'inspection auprès de {$rapport->garage->nom} a été confirmé. Le garage peut maintenant soumettre le rapport.",
                        'PAIEMENT', '/vendeur/inspections');

                } elseif (in_array($transaction->status, ['declined', 'canceled'])) {
                    $paiement->update(['statut' => 'ECHOUE']);
                }

                if ($isUserRedirect) {
                    return redirect(config('services.frontend_url') . '/vendeur/paiement-inspection-retour?rapport_id=' . $rapport->id);
                }
            }

            return response()->json(['message' => 'Callback traité'], 200);

        } catch (\Exception $e) {
            \Log::error('FedaPay callback error', ['error' => $e->getMessage(), 'transaction_id' => $transactionId]);

            if ($isUserRedirect) {
                return redirect(config('services.frontend_url') . '/acheteur/mes-reservations');
            }
            return response()->json(['message' => 'Erreur lors du traitement du callback', 'error' => $e->getMessage()], 500);
        }
    }

    // ===== VÉRIFIER PAIEMENT RÉSERVATION =====

    public function verifier(Request $request, int $reservationId): JsonResponse
    {
        $reservation = Reservation::with('annonce.vehicule.modele.marque')
            ->where('acheteur_id', $request->user()->acheteur->id)
            ->findOrFail($reservationId);

        $paiement = Paiement::where('reservation_id', $reservationId)->latest()->first();

        if (!$paiement) {
            return response()->json(['statut' => 'NON_PAYE', 'message' => 'Aucun paiement trouvé']);
        }

        try {
            if ($paiement->transaction_id) {
                $transaction = Transaction::retrieve($paiement->transaction_id);

                if ($transaction->status === 'approved' && $paiement->statut !== 'APPROUVE') {
                    $paiement->update(['statut' => 'APPROUVE']);
                    $reservation->update(['statut' => 'CONFIRMEE']);

                    $vehiculeNom = "{$reservation->annonce->vehicule->modele->marque->nom} {$reservation->annonce->vehicule->modele->nom}";

                    Notification::creer($reservation->acheteur->user_id, 'Paiement confirmé',
                        "Votre paiement pour le {$vehiculeNom} a été confirmé.", 'PAIEMENT', '/acheteur/mes-reservations');

                    Notification::creer($reservation->annonce->vendeur->user_id, 'Nouvelle réservation payée',
                        "Une réservation payée pour votre {$vehiculeNom} a été reçue.", 'RESERVATION', '/vendeur/reservations');
                }

                return response()->json(['statut' => $paiement->statut, 'transaction_status' => $transaction->status, 'reference' => $paiement->reference]);
            }

            return response()->json(['statut' => $paiement->statut, 'reference' => $paiement->reference]);

        } catch (\Exception $e) {
            \Log::error('FedaPay verification error', ['error' => $e->getMessage()]);
            return response()->json(['statut' => $paiement->statut, 'message' => 'Impossible de vérifier le statut avec FedaPay']);
        }
    }

    // ===== VÉRIFIER PAIEMENT INSPECTION =====

    public function verifierInspection(Request $request, int $rapportId): JsonResponse
    {
        $vendeur = $request->user()->vendeur;

        $rapport = RapportInspection::with('annonce')
            ->whereHas('annonce', fn($q) => $q->where('vendeur_id', $vendeur->id))
            ->findOrFail($rapportId);

        $paiement = Paiement::where('rapport_inspection_id', $rapportId)
            ->where('type', 'INSPECTION')
            ->latest()->first();

        if (!$paiement) {
            return response()->json(['statut' => 'NON_PAYE', 'message' => 'Aucun paiement trouvé']);
        }

        try {
            if ($paiement->transaction_id) {
                $transaction = Transaction::retrieve($paiement->transaction_id);

                if ($transaction->status === 'approved' && $paiement->statut !== 'APPROUVE') {
                    $paiement->update(['statut' => 'APPROUVE']);
                }

                return response()->json(['statut' => $paiement->statut, 'transaction_status' => $transaction->status, 'reference' => $paiement->reference]);
            }

            return response()->json(['statut' => $paiement->statut, 'reference' => $paiement->reference]);

        } catch (\Exception $e) {
            \Log::error('FedaPay verification error', ['error' => $e->getMessage()]);
            return response()->json(['statut' => $paiement->statut, 'message' => 'Impossible de vérifier le statut']);
        }
    }
}