<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Entry;
use App\Services\FileOrganizer;
use Carbon\Carbon;

class TestFileOrganization extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parocompta:test-file-organization';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Teste l\'organisation des fichiers par trimestre';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Test de l\'organisation des fichiers par trimestre');
        $this->info('================================================');
        $this->newLine();

        // Récupérer toutes les entries
        $entries = Entry::with(['parish', 'category'])->get();

        if ($entries->isEmpty()) {
            $this->warn('Aucune fiche comptable trouvée.');
            $this->info('Exécutez d\'abord : php artisan db:seed --class=FileOrganizationTestSeeder');
            return 0;
        }

        $this->info("📊 Analyse de {$entries->count()} fiches comptables :");
        $this->newLine();

        foreach ($entries as $entry) {
            $this->info("🔍 Fiche #{$entry->id} - {$entry->category->name}");
            $this->info("   📅 Date : {$entry->start_date}");

            // Tester les méthodes FileOrganizer
            $quarter = $entry->getQuarter();
            $weekNumber = $entry->getWeekNumber();
            $fileName = $entry->getFileName();
            $directoryPath = $entry->getDirectoryPath();
            $filePath = $entry->getFilePath();

            $this->info("   📂 Trimestre : {$quarter}");
            $this->info("   📅 Semaine : S{$weekNumber}");
            $this->info("   📄 Nom fichier : {$fileName}");
            $this->info("   📁 Dossier : {$directoryPath}");
            $this->info("   📍 Chemin complet : {$filePath}");

            // Tester la création du dossier
            if ($entry->ensureDirectoryExists()) {
                $this->info("   ✅ Dossier créé/vérifié avec succès");
            } else {
                $this->error("   ❌ Erreur lors de la création du dossier");
            }

            $this->newLine();
        }

        // Afficher la structure globale
        $this->info('📁 Structure des dossiers créés :');
        $this->newLine();

        $parishes = $entries->groupBy('parish_id');
        foreach ($parishes as $parishId => $parishEntries) {
            $parish = $parishEntries->first()->parish;
            $this->info("🏛️  {$parish->name}");
            $this->info("   📂 Base : {$parish->getStorageBasePath()}");

            $structure = FileOrganizer::getDirectoryStructure($parish);
            foreach ($structure as $quarter => $categories) {
                $this->info("   📅 {$quarter}");
                foreach ($categories as $category => $files) {
                    $this->info("      📁 {$category} (" . count($files) . " fichiers)");
                    foreach ($files as $file) {
                        $this->info("         📄 {$file}");
                    }
                }
            }
            $this->newLine();
        }

        $this->info('✅ Test terminé avec succès !');
        return 0;
    }
}
