<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Parish;
use App\Services\DefaultCategoriesService;

class ProductionSeeder extends Seeder
{
    /**
     * Seeder pour l'environnement de production
     * Crée uniquement le compte administrateur
     */
    public function run(): void
    {
        $this->command->info('🚀 Initialisation de ParoCompta pour la production...');

        // Créer le compte administrateur
        $admin = User::updateOrCreate(
            ['email' => 'admin@paro.com'],
            [
                'name' => 'Administrateur',
                'email_verified_at' => now(),
                'password' => bcrypt('P@roCompta1.0'),
                'role' => 'admin',
                'parish_id' => null,
            ]
        );

        $this->command->info('✅ Compte administrateur créé/mis à jour');
        $this->command->info('📧 Email: admin@paro.com');
        $this->command->info('🔑 Mot de passe: P@roCompta1.0');
        $this->command->newLine();

        // Créer une paroisse de test pour les tests
        $parish = Parish::create([
            'name' => 'Paroisse Test',
            'storage_path' => 'C:\ParoCompta\Test',
            'enable_payment_tracking' => true,
            'weekly_payment_amount' => 1000
        ]);

        // Créer les catégories par défaut
        DefaultCategoriesService::createDefaultCategoriesForParish($parish);

        // Créer un utilisateur de test
        $user = User::updateOrCreate(
            ['email' => 'test@paroisse.com'],
            [
                'name' => 'Utilisateur Test',
                'password' => bcrypt('password'),
                'role' => 'user',
                'parish_id' => $parish->id
            ]
        );

        $this->command->info('✅ Paroisse de test créée avec 4 catégories');
        $this->command->info('✅ Utilisateur de test créé: test@paroisse.com / password');
        $this->command->newLine();

        $this->command->info('🎯 ParoCompta est prêt pour une utilisation réelle !');
        $this->command->info('💡 Connectez-vous et commencez par créer votre première paroisse.');
    }
}
