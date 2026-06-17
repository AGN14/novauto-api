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
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $garage = GaragePartenaire::where('email', $request->email)->first();

        if (!$garage || !Hash::check($request->password, $garage->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        if (!$garage->agree) {
            return response()->json([
                'message' => 'Votre compte garage n\'est pas encore agréé par l\'administration.'
            ], 403);
        }

        $token = $garage->createToken('garage-auth-token')->plainTextToken;

        return response()->json([
            'garage' => $garage,
            'token'  => $token,
        ]);
    }

    /**
     * Inscription garage (public)
     */
    public function inscription(Request $request)
    {
        $request->validate([
            'nom'       => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'unique:garages_partenaires,email'],
            'password'  => ['required', 'string', 'min:8', 'confirmed',
                            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])/'],
            'telephone' => ['required', 'string', 'max:20'],
            'ville'     => ['required', 'string', 'max:255'],
            'adresse'   => ['required', 'string'],
        ], [
            'password.regex'  => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial.',
            'email.unique'    => 'Cet email est déjà utilisé.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        GaragePartenaire::create([
            'nom'             => $request->nom,
            'email'           => $request->email,
            'password'        => $request->password,
            'telephone'       => $request->telephone,
            'ville'           => $request->ville,
            'adresse'         => $request->adresse,
            'agree'           => false,
            'certifie'        => false,
            'statut_demande'  => 'EN_ATTENTE',
            'prix_inspection' => 2000,
        ]);

        return response()->json([
            'message' => 'Votre demande a été soumise. L\'administrateur examinera votre dossier sous 48h.',
        ], 201);
    }

    /**
     * Profil du garage connecté
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Mettre à jour le profil
     */
    public function updateProfil(Request $request)
    {
        $garage = $request->user();

        $validated = $request->validate([
            'prix_inspection' => ['sometimes', 'numeric', 'min:500', 'max:100000'],
            'telephone'       => ['sometimes', 'string', 'max:20'],
            'ville'           => ['sometimes', 'string', 'max:255'],
            'adresse'         => ['sometimes', 'string'],
        ]);

        $garage->update($validated);

        return response()->json([
            'message' => 'Profil mis à jour avec succès.',
            'garage'  => $garage->fresh(),
        ]);
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