<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Parish;
use App\Models\Category;
use App\Models\Entry;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CleanDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parocompta:clean {--force : Force la suppression sans confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nettoie complètement la base de données et ne garde que le compte administrateur';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 NETTOYAGE COMPLET DE LA BASE DE DONNÉES PAROCOMPTA');
        $this->info('================================================');

        // Vérification de sécurité
        if (!$this->option('force')) {
            $this->warn('⚠️  ATTENTION : Cette opération va supprimer TOUTES les données !');
            $this->warn('   - Toutes les paroisses');
            $this->warn('   - Toutes les fiches comptables');
            $this->warn('   - Tous les paiements');
            $this->warn('   - Tous les utilisateurs (sauf admin)');
            $this->warn('   - Tous les fichiers générés');
            $this->newLine();

            if (!$this->confirm('Êtes-vous ABSOLUMENT sûr de vouloir continuer ?')) {
                $this->info('❌ Opération annulée.');
                return 0;
            }

            if (!$this->confirm('Dernière confirmation - Supprimer TOUTES les données ?')) {
                $this->info('❌ Opération annulée.');
                return 0;
            }
        }

        $this->info('🚀 Début du nettoyage...');
        $this->newLine();

        // 1. Supprimer tous les fichiers générés
        $this->cleanGeneratedFiles();

        // 2. Vider les tables dans l'ordre (contraintes de clés étrangères)
        $this->cleanDatabaseTables();

        // 3. Recréer le compte admin
        $this->createAdminAccount();

        // 4. Réinitialiser les auto-increment
        $this->resetAutoIncrement();

        $this->newLine();
        $this->info('✅ NETTOYAGE TERMINÉ AVEC SUCCÈS !');
        $this->info('🔑 Compte admin disponible :');
        $this->info('   Email: admin@paro.com');
        $this->info('   Mot de passe: password');
        $this->newLine();
        $this->info('🎯 Votre application ParoCompta est maintenant prête pour une utilisation réelle !');

        return 0;
    }

    private function cleanGeneratedFiles()
    {
        $this->info('📁 Suppression des fichiers générés...');

        try {
            // Supprimer tous les fichiers dans storage/app/public
            $files = Storage::disk('public')->allFiles();
            if (!empty($files)) {
                Storage::disk('public')->delete($files);
                $filesCount = count($files);
                $this->info("   ✓ {$filesCount} fichiers supprimés du stockage public");
            }

            // Supprimer tous les dossiers vides
            $directories = Storage::disk('public')->allDirectories();
            foreach (array_reverse($directories) as $directory) {
                if (empty(Storage::disk('public')->allFiles($directory))) {
                    Storage::disk('public')->deleteDirectory($directory);
                }
            }

            $this->info('   ✓ Fichiers générés supprimés');
        } catch (\Exception $e) {
            $this->warn("   ⚠️  Erreur lors de la suppression des fichiers : {$e->getMessage()}");
        }
    }

    private function cleanDatabaseTables()
    {
        $this->info('🗄️  Nettoyage des tables de la base de données...');

        // Désactiver les contraintes de clés étrangères temporairement
        DB::statement('PRAGMA foreign_keys = OFF');

        try {
            // Supprimer dans l'ordre des dépendances
            $deletedPayments = Payment::count();
            Payment::truncate();
            $this->info("   ✓ {$deletedPayments} paiements supprimés");

            $deletedEntries = Entry::count();
            Entry::truncate();
            $this->info("   ✓ {$deletedEntries} fiches comptables supprimées");

            $deletedCategories = Category::count();
            Category::truncate();
            $this->info("   ✓ {$deletedCategories} catégories supprimées");

            $deletedParishes = Parish::count();
            Parish::truncate();
            $this->info("   ✓ {$deletedParishes} paroisses supprimées");

            // Supprimer tous les utilisateurs sauf admin
            $deletedUsers = User::where('email', '!=', 'admin@paro.com')->count();
            User::where('email', '!=', 'admin@paro.com')->delete();
            $this->info("   ✓ {$deletedUsers} utilisateurs supprimés (admin conservé)");

        } finally {
            // Réactiver les contraintes de clés étrangères
            DB::statement('PRAGMA foreign_keys = ON');
        }
    }

    private function createAdminAccount()
    {
        $this->info('👤 Vérification du compte administrateur...');

        $admin = User::where('email', 'admin@paro.com')->first();

        if (!$admin) {
            $admin = User::create([
                'name' => 'Administrateur',
                'email' => 'admin@paro.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'role' => 'admin',
                'parish_id' => null,
            ]);
            $this->info('   ✓ Compte administrateur créé');
        } else {
            // Réinitialiser le compte admin
            $admin->update([
                'name' => 'Administrateur',
                'role' => 'admin',
                'parish_id' => null,
                'password' => bcrypt('password'),
            ]);
            $this->info('   ✓ Compte administrateur réinitialisé');
        }
    }

    private function resetAutoIncrement()
    {
        $this->info('🔄 Réinitialisation des compteurs auto-increment...');

        try {
            // Pour SQLite, on utilise UPDATE sqlite_sequence
            DB::statement("UPDATE sqlite_sequence SET seq = 0 WHERE name IN ('users', 'parishes', 'categories', 'entries', 'payments')");
            $this->info('   ✓ Compteurs réinitialisés');
        } catch (\Exception $e) {
            $this->warn("   ⚠️  Impossible de réinitialiser les compteurs : {$e->getMessage()}");
        }
    }
}
