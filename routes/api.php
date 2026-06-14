<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AnnonceController;
use App\Http\Controllers\Api\MarqueController;
use App\Http\Controllers\Api\VinController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\RendezVousController;
use App\Http\Controllers\Api\InspectionController;
use App\Http\Controllers\Api\AvisController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PaiementController;
use App\Http\Controllers\Api\GarageAuthController;
use App\Http\Controllers\Api\DisponibiliteController;


// Auth
Route::prefix('auth')->group(function () {
    Route::post('/register',        [AuthController::class, 'register']);
    Route::post('/login',           [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password',  [AuthController::class, 'resetPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',      [AuthController::class, 'me']);
    });
});

// Catalogue (public)
Route::prefix('catalogue')->group(function () {
    Route::get('/',         [AnnonceController::class, 'index']);
    Route::get('/featured', [AnnonceController::class, 'featured']);
    Route::get('/{id}',     [AnnonceController::class, 'show']);
    Route::get('/{id}/avis', [AvisController::class, 'index']);
    Route::get('/{annonceId}/disponibilites', [DisponibiliteController::class, 'disponibilitesParAnnonce']);
});

// Marques (public)
Route::prefix('marques')->group(function () {
    Route::get('/',             [MarqueController::class, 'index']);
    Route::get('/{id}/modeles', [MarqueController::class, 'modeles']);
});

// VIN (public)
Route::post('/vin/decode', [VinController::class, 'decode']);

// Garages (public)
Route::get('/garages', [InspectionController::class, 'garages']);
Route::get('/garages/{id}/disponibilites', [DisponibiliteController::class, 'disponibilitesGaragePublic']);

// Webhook FedaPay (public)
Route::match(['get', 'post'], '/paiements/callback', [PaiementController::class, 'callback']);

// Routes protégées (utilisateurs)
Route::middleware('auth:sanctum')->group(function () {

    // Vendeur - annonces
    Route::prefix('vendeur')->group(function () {
        Route::get('/annonces',         [AnnonceController::class, 'mesAnnonces']);
        Route::post('/annonces',        [AnnonceController::class, 'store']);
        Route::put('/annonces/{id}',    [AnnonceController::class, 'update']);
        Route::delete('/annonces/{id}', [AnnonceController::class, 'destroy']);
    });

    // Acheteur
    Route::prefix('acheteur')->group(function () {
        Route::post('/rendez-vous',               [RendezVousController::class, 'store']);
        Route::get('/rendez-vous',                [RendezVousController::class, 'index']);
        Route::get('/rendez-vous/{id}',           [RendezVousController::class, 'show']);
        Route::post('/rendez-vous/{id}/annuler',  [RendezVousController::class, 'cancel']);

        Route::post('/reservations',              [ReservationController::class, 'store']);
        Route::get('/reservations',               [ReservationController::class, 'index']);
        Route::get('/reservations/{id}',          [ReservationController::class, 'show']);
        Route::post('/reservations/{id}/annuler', [ReservationController::class, 'cancel']);

        Route::post('/avis', [AvisController::class, 'store']);
        Route::get('/avis',  [AvisController::class, 'mesAvis']);
    });

    // Vendeur - tout le reste
    Route::prefix('vendeur')->group(function () {
        Route::get('/rendez-vous',                     [RendezVousController::class, 'vendeurRendezVous']);
        Route::post('/rendez-vous/{id}/confirmer',     [RendezVousController::class, 'confirmer']);
        Route::post('/rendez-vous/{id}/proposer-date', [RendezVousController::class, 'proposerAutreDate']);
        Route::post('/rendez-vous/{id}/annuler',       [RendezVousController::class, 'annulerVendeur']);

        Route::get('/reservations',                    [ReservationController::class, 'vendeurReservations']);
        Route::post('/reservations/{id}/confirmer',    [ReservationController::class, 'confirmer']);
        Route::post('/reservations/{id}/annuler',      [ReservationController::class, 'annulerVendeur']);

        Route::post('/avis/{id}/signaler', [AvisController::class, 'signalerAvis']);

        // Disponibilités vendeur
        Route::get('/disponibilites',          [DisponibiliteController::class, 'vendeurDisponibilites']);
        Route::post('/disponibilites',         [DisponibiliteController::class, 'creerDisponibilite']);
        Route::post('/disponibilites/batch',   [DisponibiliteController::class, 'creerDisponibilitesBatch']);
        Route::put('/disponibilites/{id}',     [DisponibiliteController::class, 'modifierDisponibilite']);
        Route::delete('/disponibilites/{id}',  [DisponibiliteController::class, 'supprimerDisponibilite']);

        // Inspections vendeur
        Route::post('/inspections',                              [InspectionController::class, 'demanderInspection']);
        Route::get('/inspections',                               [InspectionController::class, 'mesInspections']);
        Route::post('/inspections/{id}/confirmer-presence',      [InspectionController::class, 'vendeurConfirmerPresence']);
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/',              [NotificationController::class, 'index']);
        Route::get('/non-lues',      [NotificationController::class, 'nonLues']);
        Route::post('/{id}/lue',     [NotificationController::class, 'marquerLue']);
        Route::post('/toutes-lues',  [NotificationController::class, 'marquerToutesLues']);
        Route::delete('/{id}',       [NotificationController::class, 'destroy']);
    });

    // Paiements
    Route::prefix('paiements')->group(function () {
        Route::post('/initier',                        [PaiementController::class, 'initier']);
        Route::get('/verifier/{reservationId}',        [PaiementController::class, 'verifier']);
        Route::post('/initier-inspection',             [PaiementController::class, 'initierInspection']);
        Route::get('/verifier-inspection/{rapportId}', [PaiementController::class, 'verifierInspection']);
    });
});

// Admin
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/stats',                          [AdminController::class, 'stats']);
    Route::get('/annonces',                       [AdminController::class, 'annonces']);
    Route::get('/vendeurs',                       [AdminController::class, 'vendeurs']);
    Route::put('/annonces/{id}',                  [AdminController::class, 'updateAnnonce']);
    Route::delete('/annonces/{id}',               [AdminController::class, 'deleteAnnonce']);
    Route::post('/vendeurs/{id}/certifier',       [AdminController::class, 'certifierVendeur']);
    Route::post('/vendeurs/{id}/suspendre',       [AdminController::class, 'suspendreVendeur']);
    Route::get('/garages',                        [AdminController::class, 'garages']);
    Route::post('/garages/{id}/certifier',        [AdminController::class, 'certifierGarage']);
    Route::post('/garages/{id}/suspendre',        [AdminController::class, 'suspendreGarage']);
    Route::get('/avis/signales',                  [AvisController::class, 'avisSignales']);
    Route::post('/avis/{id}/supprimer',           [AvisController::class, 'supprimerAvis']);
    Route::post('/avis/{id}/rejeter-signalement', [AvisController::class, 'rejeterSignalement']);
});

// Garage Partenaire
Route::prefix('garage')->group(function () {
    Route::post('/login', [GarageAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me',      [GarageAuthController::class, 'me']);
        Route::post('/logout', [GarageAuthController::class, 'logout']);
        Route::put('/profil',  [GarageAuthController::class, 'updateProfil']);

        Route::get('/inspections',                      [InspectionController::class, 'demandesEnAttente']);
        Route::post('/inspections/{id}/generer-code',   [InspectionController::class, 'garageGenererCode']);
        Route::post('/inspections/{id}/soumettre',      [InspectionController::class, 'soumettreRapport']);
        Route::post('/inspections/{id}/rejeter',        [InspectionController::class, 'rejeterInspection']);

        Route::get('/disponibilites',                   [DisponibiliteController::class, 'garageDisponibilites']);
        Route::post('/disponibilites',                  [DisponibiliteController::class, 'creerDisponibiliteGarage']);
        Route::post('/disponibilites/batch',            [DisponibiliteController::class, 'creerDisponibilitesGarageBatch']);
        Route::delete('/disponibilites/{id}',           [DisponibiliteController::class, 'supprimerDisponibiliteGarage']);
    });
});