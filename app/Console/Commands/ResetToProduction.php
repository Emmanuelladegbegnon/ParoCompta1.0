<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetToProduction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parocompta:reset {--force : Force la réinitialisation sans confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Réinitialise complètement ParoCompta pour une utilisation en production';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 RÉINITIALISATION COMPLÈTE DE PAROCOMPTA');
        $this->info('========================================');
        $this->newLine();

        if (!$this->option('force')) {
            $this->warn('Cette commande va :');
            $this->warn('1. Nettoyer complètement la base de données');
            $this->warn('2. Supprimer tous les fichiers générés');
            $this->warn('3. Recréer uniquement le compte administrateur');
            $this->newLine();

            if (!$this->confirm('Continuer ?')) {
                $this->info('❌ Opération annulée.');
                return 0;
            }
        }

        // Étape 1: Nettoyer la base de données
        $this->info('🧹 Étape 1/2 : Nettoyage de la base de données...');
        $this->call('parocompta:clean', ['--force' => true]);

        // Étape 2: Initialiser pour la production
        $this->info('🚀 Étape 2/2 : Initialisation pour la production...');
        $this->call('db:seed', ['--class' => 'ProductionSeeder']);

        $this->newLine();
        $this->info('✅ RÉINITIALISATION TERMINÉE !');
        $this->info('🎯 ParoCompta est maintenant prêt pour une utilisation réelle.');
        $this->newLine();
        $this->info('🔑 Connectez-vous avec :');
        $this->info('   Email: admin@paro.com');
        $this->info('   Mot de passe: password');

        return 0;
    }
}
