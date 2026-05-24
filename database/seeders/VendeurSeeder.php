<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendeur;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class VendeurSeeder extends Seeder
{
    public function run(): void
    {
        $vendeurs = [
            ['prenom' => 'Kofi', 'nom' => 'Mensah', 'email' => 'kofi.mensah@gmail.com', 'telephone' => '+22997001122'],
            ['prenom' => 'Adjoa', 'nom' => 'Koffi', 'email' => 'adjoa.koffi@gmail.com', 'telephone' => '+22996223344'],
            ['prenom' => 'Sékou', 'nom' => 'Traoré', 'email' => 'sekou.traore@gmail.com', 'telephone' => '+22995445566'],
            ['prenom' => 'Fatou', 'nom' => 'Diallo', 'email' => 'fatou.diallo@gmail.com', 'telephone' => '+22991334455'],
            ['prenom' => 'Moussa', 'nom' => 'Coulibaly', 'email' => 'moussa.coulibaly@gmail.com', 'telephone' => '+22997889900'],
            ['prenom' => 'Ama', 'nom' => 'Asante', 'email' => 'ama.asante@gmail.com', 'telephone' => '+22996778899'],
            ['prenom' => 'Ibrahim', 'nom' => 'Sawadogo', 'email' => 'ibrahim.sawadogo@gmail.com', 'telephone' => '+22995667788'],
            ['prenom' => 'Aïcha', 'nom' => 'Touré', 'email' => 'aicha.toure@gmail.com', 'telephone' => '+22994556677'],
            ['prenom' => 'Kwame', 'nom' => 'Osei', 'email' => 'kwame.osei@gmail.com', 'telephone' => '+22993445566'],
            ['prenom' => 'Mariam', 'nom' => 'Bah', 'email' => 'mariam.bah@gmail.com', 'telephone' => '+22992334455'],
            ['prenom' => 'Yao', 'nom' => 'Agbeko', 'email' => 'yao.agbeko@gmail.com', 'telephone' => '+22991223344'],
            ['prenom' => 'Aminata', 'nom' => 'Sylla', 'email' => 'aminata.sylla@gmail.com', 'telephone' => '+22990112233'],
        ];

        $villes = ['Cotonou', 'Porto-Novo'];

        foreach ($vendeurs as $vendeurData) {
            $user = User::firstOrCreate(
                ['email' => $vendeurData['email']],
                [
                    'nom' => $vendeurData['prenom'] . ' ' . $vendeurData['nom'],
                    'password' => Hash::make('password123'),
                    'role' => 'VENDEUR',
                    'tel' => $vendeurData['telephone'],
                ]
            );

            Vendeur::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'type_compte' => 'PARTICULIER',
                    'certifie' => false,
                ]
            );

            $this->command->info("Vendeur créé : {$vendeurData['prenom']} {$vendeurData['nom']}");
        }
    }
}
