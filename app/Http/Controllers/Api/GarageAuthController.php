<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GaragePartenaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class GarageAuthController extends Controller
{
    /**
     * Connexion garage
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $garage = GaragePartenaire::where('email', $request->email)->first();

        if (!$garage || !Hash::check($request->password, $garage->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        // Vérifier que le garage est agréé
        if (!$garage->agree) {
            return response()->json([
                'message' => 'Votre compte garage n\'est pas encore agréé par l\'administration.'
            ], 403);
        }

        $token = $garage->createToken('garage-auth-token')->plainTextToken;

        return response()->json([
            'garage' => $garage,
            'token' => $token,
        ]);
    }

    /**
     * Profil du garage connecté
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Déconnexion garage
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie.'
        ]);
    }
}
