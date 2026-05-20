<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AnnonceController;
use App\Http\Controllers\Api\MarqueController;
use App\Http\Controllers\Api\VinController;
use Illuminate\Support\Facades\Route;

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
    Route::get('/',          [AnnonceController::class, 'index']);
    Route::get('/featured',  [AnnonceController::class, 'featured']);
    Route::get('/{id}',      [AnnonceController::class, 'show']);
});

Route::prefix('marques')->group(function () {
    Route::get('/',              [MarqueController::class, 'index']);
    Route::get('/{id}/modeles',  [MarqueController::class, 'modeles']);
});

Route::prefix('vin')->group(function () {
    Route::post('/decode', [VinController::class, 'decode']);
});