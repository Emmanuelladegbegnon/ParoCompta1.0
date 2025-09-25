<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Parish;
use App\Models\Category;
use App\Models\Entry;
use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CleanDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🧹 Nettoyage de la base de données...');

        // Supprimer toutes les données existantes (sauf les utilisateurs admin)
        Payment::truncate();
        Entry::truncate();
        Category::truncate();

        // Supprimer les paroisses de test
        Parish::where('name', '!=', 'Paroisse Principale')->delete();

        // Supprimer les utilisateurs de test (garder seulement l'admin)
        User::where('email', '!=', 'admin@paro.com')->delete();

        $this->command->info('✅ Données de test supprimées');

        // Créer une paroisse principale vide pour commencer
        $parish = Parish::firstOrCreate(
            ['name' => 'Paroisse Principale'],
            [
                'enable_payment_tracking' => true,
                'weekly_payment_amount' => 0,
                'storage_path' => 'public',
                // Configuration factures vide pour saisie manuelle
                'invoice_company_name' => 'ParoCompta Services',
                'invoice_company_description' => 'Système de Suivi des Recettes Paroissiales',
                'invoice_company_address' => null,
                'invoice_company_phone' => null,
                'invoice_company_email' => 'contact@parocompta.local',
                'invoice_company_ifu' => null,
                'invoice_parish_address' => null,
                'invoice_parish_phone' => null,
                'invoice_parish_contact_name' => null,
                'invoice_parish_contact_phone' => null,
                'invoice_payment_method' => 'Espèces',
                'invoice_legal_mentions' => 'Facture établie selon les normes en vigueur. Application destinée au suivi des recettes et quêtes paroissiales uniquement.',
            ]
        );

        // S'assurer que l'admin existe
        $admin = User::firstOrCreate(
            ['email' => 'admin@paro.com'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('P@roCompta1.0'),
                'role' => 'admin',
                'parish_id' => null, // Admin n'est pas lié à une paroisse spécifique
            ]
        );

        $this->command->info('✅ Paroisse principale créée : ' . $parish->name);
        $this->command->info('✅ Administrateur configuré : ' . $admin->email);
        $this->command->info('');
        $this->command->info('🎯 Base de données nettoyée et prête pour la saisie !');
        $this->command->info('');
        $this->command->info('📋 Prochaines étapes :');
        $this->command->info('   1. Connectez-vous avec : admin@paro.com / P@roCompta1.0');
        $this->command->info('   2. Allez dans "Outils Admin" > "Configuration Factures"');
        $this->command->info('   3. Configurez vos informations de facturation');
        $this->command->info('   4. Créez vos paroisses et utilisateurs');
        $this->command->info('   5. Commencez la saisie des données');
    }
}
