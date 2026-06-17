<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->register($request->validated());
            return response()->json($result, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de l\'inscription.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->validated());
            return response()->json($result, 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Email ou mot de passe incorrect.',
                'errors'  => $e->errors()
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());
        return response()->json(['message' => 'Déconnexion réussie.'], 200);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user()->load(['vendeur', 'acheteur'])
        ], 200);
    }

    public function updateProfil(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'nom'               => ['sometimes', 'string', 'min:2', 'max:255'],
            'tel'               => ['sometimes', 'string', 'min:8'],
            'latitude'          => ['nullable', 'numeric'],
            'longitude'         => ['nullable', 'numeric'],
            'adresse_structure' => ['nullable', 'string'],
        ]);

        // Mettre à jour l'utilisateur
        $user->update([
            'nom' => $validated['nom'] ?? $user->nom,
            'tel' => $validated['tel'] ?? $user->tel,
        ]);

        // Mettre à jour le vendeur si applicable
        if ($user->role === 'VENDEUR' && $user->vendeur) {
            $user->vendeur->update([
                'latitude'          => $validated['latitude'] ?? $user->vendeur->latitude,
                'longitude'         => $validated['longitude'] ?? $user->vendeur->longitude,
                'adresse_structure' => $validated['adresse_structure'] ?? $user->vendeur->adresse_structure,
            ]);
        }

        return response()->json([
            'message' => 'Profil mis à jour avec succès.',
            'user'    => $user->fresh()->load(['vendeur', 'acheteur']),
        ]);
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->forgotPassword($request->email);
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->resetPassword($request->validated());
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}