<?php

namespace App\Services;

use App\Mail\LoginNotificationMail;
use App\Mail\WelcomeMail;
use App\Models\Acheteur;
use App\Models\User;
use App\Models\Vendeur;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data): array
    {
        $user = User::create([
            'nom'      => $data['nom'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
            'tel'      => $data['tel'],
        ]);

        if ($data['role'] === 'ACHETEUR') {
            Acheteur::create(['user_id' => $user->id]);
        }

        if ($data['role'] === 'VENDEUR') {
            Vendeur::create([
                'user_id'           => $user->id,
                'type_compte'       => $data['type_compte'],
                'ifu'               => $data['ifu'] ?? null,
                'nom_structure'     => $data['nom_structure'] ?? null,
                'adresse_structure' => $data['adresse_structure'] ?? null,
                'rccm'              => $data['rccm'] ?? null,
                'type_structure'    => $data['type_structure'] ?? null,
                'latitude'          => $data['latitude'] ?? null,
                'longitude'         => $data['longitude'] ?? null,
            ]);
        }

        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (\Exception $e) {
            \Log::error('Erreur envoi email bienvenue: ' . $e->getMessage());
        }

        $token = $user->createToken('novauto_token')->plainTextToken;

        return [
            'message' => 'Compte créé avec succès.',
            'token'   => $token,
            'user'    => $this->formatUser($user),
        ];
    }

    public function login(array $data): array
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email ou mot de passe incorrect.'],
            ]);
        }

        $user->tokens()->delete();
        $token = $user->createToken('novauto_token')->plainTextToken;

        return [
            'message' => 'Connexion réussie.',
            'token'   => $token,
            'user'    => $this->formatUser($user),
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function forgotPassword(string $email): array
    {
        $status = Password::sendResetLink(['email' => $email]);

        if ($status === Password::RESET_LINK_SENT) {
            return ['message' => 'Un lien de réinitialisation a été envoyé à votre email.'];
        }

        throw new \Exception('Impossible d\'envoyer le lien de réinitialisation.');
    }

    public function resetPassword(array $data): array
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            throw new \Exception('Utilisateur introuvable.');
        }

        if (!Password::tokenExists($user, $data['token'])) {
            throw new \Exception('Token invalide ou expiré.');
        }

        $user->forceFill([
            'password' => Hash::make($data['password']),
        ])->save();

        Password::deleteToken($user);
        $user->tokens()->delete();

        return ['message' => 'Mot de passe réinitialisé avec succès.'];
    }

    private function formatUser(User $user): array
    {
        $data = [
            'id'            => $user->id,
            'nom'           => $user->nom,
            'email'         => $user->email,
            'role'          => $user->role,
            'tel'           => $user->tel,
            'mfa_actif'     => $user->mfa_actif,
            'date_creation' => $user->created_at,
        ];

        if ($user->role === 'VENDEUR') {
            $data['vendeur'] = $user->vendeur;
        }

        if ($user->role === 'ACHETEUR') {
            $data['acheteur'] = $user->acheteur;
        }

        return $data;
    }
}