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

class CompleteCleanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🧹 Nettoyage COMPLET de la base de données...');

        // Supprimer TOUTES les données
        Payment::truncate();
        Entry::truncate();
        Category::truncate();
        Parish::truncate();
        User::truncate();

        $this->command->info('✅ Toutes les données supprimées');

        // Créer UNIQUEMENT l'administrateur
        $admin = User::create([
            'name' => 'Administrateur',
            'email' => 'admin@paro.com',
            'password' => Hash::make('P@roCompta1.0'),
            'role' => 'admin',
            'parish_id' => null, // Admin n'est pas lié à une paroisse
        ]);

        $this->command->info('✅ Administrateur créé : ' . $admin->email);
        $this->command->info('');
        $this->command->info('🎯 Base de données COMPLÈTEMENT vide !');
        $this->command->info('');
        $this->command->info('📋 État final :');
        $this->command->info('   👥 Utilisateurs : 1 (admin uniquement)');
        $this->command->info('   ⛪ Paroisses : 0 (aucune)');
        $this->command->info('   📂 Catégories : 0 (aucune)');
        $this->command->info('   📝 Fiches : 0 (aucune)');
        $this->command->info('   💰 Paiements : 0 (aucun)');
        $this->command->info('');
        $this->command->info('🚀 Prochaines étapes :');
        $this->command->info('   1. Connectez-vous : admin@paro.com / P@roCompta1.0');
        $this->command->info('   2. Créez vos paroisses via "Paroisses"');
        $this->command->info('   3. Configurez les factures via "Outils Admin"');
        $this->command->info('   4. Ajoutez vos utilisateurs');
        $this->command->info('   5. Commencez la saisie');
    }
}
