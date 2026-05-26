<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanDatabase extends Command
{
    protected $signature = 'db:clean';
    protected $description = 'Nettoyer toutes les tables en gardant uniquement les administrateurs';

    public function handle(): int
    {
        $this->info('🧹 Démarrage du nettoyage de la base de données...');
        $this->newLine();

        if (!$this->confirm('⚠️  Cette action va supprimer toutes les données (sauf les admins). Continuer ?')) {
            $this->warn('Nettoyage annulé.');
            return Command::FAILURE;
        }

        try {
            // Désactiver les contraintes de clés étrangères
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            $this->info('✓ Contraintes de clés étrangères désactivées');

            // Vider les tables dans l'ordre
            $this->truncateTable('avis');
            $this->truncateTable('paiements');
            $this->truncateTable('reservations');
            $this->truncateTable('rendez_vous');
            $this->truncateTable('rapports_inspection');
            $this->truncateTable('annonces');
            $this->truncateTable('vehicules');
            $this->truncateTable('modeles');
            $this->truncateTable('marques');
            $this->truncateTable('vendeurs');
            $this->truncateTable('acheteurs');
            $this->truncateTable('personal_access_tokens');

            // Supprimer uniquement les users non-admin
            $deletedUsers = DB::table('users')->whereNotIn('role', ['ADMINISTRATEUR'])->delete();
            $this->info("✓ Users non-admin supprimés : {$deletedUsers}");

            // Réactiver les contraintes
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->info('✓ Contraintes de clés étrangères réactivées');

            $this->newLine();
            $this->displayStats();

            $this->newLine();
            $this->info('✅ Nettoyage terminé avec succès !');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->error('❌ Erreur : ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function truncateTable(string $table): void
    {
        try {
            DB::table($table)->truncate();
            $this->info("✓ Table '{$table}' vidée");
        } catch (\Exception $e) {
            $this->warn("⚠️  Impossible de vider '{$table}' : " . $e->getMessage());
        }
    }

    private function displayStats(): void
    {
        $this->info('📊 Statistiques après nettoyage :');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        $tables = [
            'users' => 'Users',
            'vendeurs' => 'Vendeurs',
            'acheteurs' => 'Acheteurs',
            'marques' => 'Marques',
            'modeles' => 'Modèles',
            'vehicules' => 'Véhicules',
            'annonces' => 'Annonces',
            'reservations' => 'Réservations',
            'rendez_vous' => 'Rendez-vous',
            'avis' => 'Avis',
            'paiements' => 'Paiements',
        ];

        foreach ($tables as $table => $label) {
            $count = DB::table($table)->count();
            $this->line(sprintf('  %-20s : %d', $label, $count));
        }
    }
}
