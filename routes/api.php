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

Route::prefix('catalogue')->group(function () {
    Route::get('/',         [AnnonceController::class, 'index']);
    Route::get('/featured', [AnnonceController::class, 'featured']);
    Route::get('/{id}',     [AnnonceController::class, 'show']);
    Route::get('/{id}/avis', [AvisController::class, 'index']);
});

Route::prefix('marques')->group(function () {
    Route::get('/',             [MarqueController::class, 'index']);
    Route::get('/{id}/modeles', [MarqueController::class, 'modeles']);
});

Route::post('/vin/decode', [VinController::class, 'decode']);

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('vendeur')->group(function () {
        Route::get('/annonces',        [AnnonceController::class, 'mesAnnonces']);
        Route::post('/annonces',       [AnnonceController::class, 'store']);
        Route::put('/annonces/{id}',   [AnnonceController::class, 'update']);
        Route::delete('/annonces/{id}',[AnnonceController::class, 'destroy']);
    });
});

Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    Route::get('/stats',                    [AdminController::class, 'stats']);
    Route::get('/annonces',                 [AdminController::class, 'annonces']);
    Route::get('/vendeurs',                 [AdminController::class, 'vendeurs']);
    Route::put('/annonces/{id}',            [AdminController::class, 'updateAnnonce']);
    Route::delete('/annonces/{id}',         [AdminController::class, 'deleteAnnonce']);
    Route::post('/vendeurs/{id}/certifier', [AdminController::class, 'certifierVendeur']);
    Route::post('/vendeurs/{id}/suspendre', [AdminController::class, 'suspendreVendeur']);

    Route::get('/avis/signales',                [AvisController::class, 'avisSignales']);
    Route::post('/avis/{id}/supprimer',         [AvisController::class, 'supprimerAvis']);
    Route::post('/avis/{id}/rejeter-signalement', [AvisController::class, 'rejeterSignalement']);
});

Route::middleware('auth:sanctum')->group(function () {
    // Acheteur
    Route::prefix('acheteur')->group(function () {
        Route::post('/rendez-vous',                  [RendezVousController::class, 'store']);
        Route::get('/rendez-vous',                   [RendezVousController::class, 'index']);
        Route::get('/rendez-vous/{id}',              [RendezVousController::class, 'show']);
        Route::post('/rendez-vous/{id}/annuler',     [RendezVousController::class, 'cancel']);

        Route::post('/reservations',                 [ReservationController::class, 'store']);
        Route::get('/reservations',                  [ReservationController::class, 'index']);
        Route::get('/reservations/{id}',             [ReservationController::class, 'show']);
        Route::post('/reservations/{id}/annuler',    [ReservationController::class, 'cancel']);

        Route::post('/avis',                         [AvisController::class, 'store']);
        Route::get('/avis',                          [AvisController::class, 'mesAvis']);
    });

    // Vendeur
    Route::prefix('vendeur')->group(function () {
        Route::get('/rendez-vous',                       [RendezVousController::class, 'vendeurRendezVous']);
        Route::post('/rendez-vous/{id}/confirmer',       [RendezVousController::class, 'confirmer']);
        Route::post('/rendez-vous/{id}/proposer-date',   [RendezVousController::class, 'proposerAutreDate']);
        Route::post('/rendez-vous/{id}/annuler',         [RendezVousController::class, 'annulerVendeur']);

        Route::get('/reservations',                      [ReservationController::class, 'vendeurReservations']);
        Route::post('/reservations/{id}/confirmer',      [ReservationController::class, 'confirmer']);
        Route::post('/reservations/{id}/annuler',        [ReservationController::class, 'annulerVendeur']);

        Route::post('/avis/{id}/signaler',               [AvisController::class, 'signalerAvis']);
    });
});

Route::get('/garages', [InspectionController::class, 'garages']);

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('vendeur')->group(function () {
        Route::post('/inspections',     [InspectionController::class, 'demanderInspection']);
        Route::get('/inspections',      [InspectionController::class, 'mesInspections']);
    });
});