<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Acheteur;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AcheteurSeeder extends Seeder
{
    public function run(): void
    {
        $acheteurs = [
            ['prenom' => 'Jean', 'nom' => 'Ahouandjinou', 'email' => 'jean.ahouandjinou@gmail.com'],
            ['prenom' => 'Marie', 'nom' => 'Dossou', 'email' => 'marie.dossou@gmail.com'],
            ['prenom' => 'Paul', 'nom' => 'Gbènou', 'email' => 'paul.gbenou@gmail.com'],
            ['prenom' => 'Sophie', 'nom' => 'Hounsou', 'email' => 'sophie.hounsou@gmail.com'],
            ['prenom' => 'Marc', 'nom' => 'Vodounou', 'email' => 'marc.vodounou@gmail.com'],
            ['prenom' => 'Claire', 'nom' => 'Dègbé', 'email' => 'claire.degbe@gmail.com'],
            ['prenom' => 'Luc', 'nom' => 'Azonnou', 'email' => 'luc.azonnou@gmail.com'],
            ['prenom' => 'Adèle', 'nom' => 'Kpossou', 'email' => 'adele.kpossou@gmail.com'],
            ['prenom' => 'Pierre', 'nom' => 'Zinsou', 'email' => 'pierre.zinsou@gmail.com'],
            ['prenom' => 'Nadège', 'nom' => 'Agossou', 'email' => 'nadege.agossou@gmail.com'],
            ['prenom' => 'Olivier', 'nom' => 'Hounkpatin', 'email' => 'olivier.hounkpatin@gmail.com'],
            ['prenom' => 'Estelle', 'nom' => 'Dako', 'email' => 'estelle.dako@gmail.com'],
            ['prenom' => 'René', 'nom' => 'Ahossi', 'email' => 'rene.ahossi@gmail.com'],
            ['prenom' => 'Carine', 'nom' => 'Biotou', 'email' => 'carine.biotou@gmail.com'],
            ['prenom' => 'Gilles', 'nom' => 'Gandaho', 'email' => 'gilles.gandaho@gmail.com'],
            ['prenom' => 'Rosine', 'nom' => 'Djossou', 'email' => 'rosine.djossou@gmail.com'],
            ['prenom' => 'Théodore', 'nom' => 'Amoussou', 'email' => 'theodore.amoussou@gmail.com'],
            ['prenom' => 'Vanessa', 'nom' => 'Hounsa', 'email' => 'vanessa.hounsa@gmail.com'],
            ['prenom' => 'Alexis', 'nom' => 'Glèlè', 'email' => 'alexis.glele@gmail.com'],
            ['prenom' => 'Patricia', 'nom' => 'Segla', 'email' => 'patricia.segla@gmail.com'],
        ];

        foreach ($acheteurs as $acheteurData) {
            $user = User::firstOrCreate(
                ['email' => $acheteurData['email']],
                [
                    'nom' => $acheteurData['prenom'] . ' ' . $acheteurData['nom'],
                    'password' => Hash::make('password123'),
                    'role' => 'ACHETEUR',
                    'tel' => '+229' . rand(90000000, 99999999),
                ]
            );

            Acheteur::firstOrCreate(
                ['user_id' => $user->id]
            );

            $this->command->info("Acheteur créé : {$acheteurData['prenom']} {$acheteurData['nom']}");
        }
    }
}
