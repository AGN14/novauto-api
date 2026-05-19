<?php

namespace App\Http\Controllers\Api;

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
}