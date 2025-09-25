<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Parish;
use App\Models\Entry;
use App\Models\Payment;
use Carbon\Carbon;

class TestPaymentSeeder extends Seeder
{
    /**
     * Créer des données de test pour les paiements et factures
     */
    public function run(): void
    {
        $this->command->info('🧪 Création de données de test pour les paiements...');

        // Récupérer l'utilisateur de test
        $user = User::where('email', 'test@paroisse.com')->first();
        
        if (!$user) {
            $this->command->error('Utilisateur de test introuvable. Exécutez d\'abord ProductionSeeder.');
            return;
        }

        $parish = $user->parish;
        $this->command->info("Utilisateur: {$user->name}");
        $this->command->info("Paroisse: {$parish->name}");

        // Créer quelques fiches comptables pour ce mois
        $category = $parish->categories()->first();
        $this->command->info("Catégorie: {$category->name}");

        // Créer 3 fiches pour septembre 2025
        for ($i = 1; $i <= 3; $i++) {
            $entry = Entry::create([
                'category_id' => $category->id,
                'parish_id' => $parish->id,
                'user_id' => $user->id,
                'week_label' => 'S' . $i,
                'start_date' => Carbon::create(2025, 9, $i * 7),
                'end_date' => Carbon::create(2025, 9, $i * 7 + 6),
                'data_json' => json_encode(['billets_1000' => 10, 'total' => 10000])
            ]);
            $this->command->info("Fiche créée: {$entry->week_label}");
        }

        // Créer un paiement de test
        $payment = Payment::create([
            'user_id' => $user->id,
            'parish_id' => $parish->id,
            'period' => '2025-09',
            'weeks_worked' => 3,
            'amount_due' => 3000,
            'amount_paid' => 1500,
            'amount_received' => 1500,
            'payment_date' => now(),
            'payment_method' => 'Virement bancaire',
            'notes' => 'Paiement partiel pour septembre 2025'
        ]);

        $this->command->info("Paiement créé avec ID: {$payment->id}");
        $this->command->info("Montant: {$payment->amount_received} FCFA");

        // Créer un deuxième paiement avec facture déjà générée
        $payment2 = Payment::create([
            'user_id' => $user->id,
            'parish_id' => $parish->id,
            'period' => '2025-08',
            'weeks_worked' => 4,
            'amount_due' => 4000,
            'amount_paid' => 4000,
            'amount_received' => 4000,
            'payment_date' => Carbon::create(2025, 8, 30),
            'payment_method' => 'Espèces',
            'notes' => 'Paiement complet pour août 2025',
            'invoice_number' => 'FCT-2025-08-0001',
            'invoice_file' => 'FACTURES_PAROCOMPTA/2025/08/Facture_FCT-2025-08-0001.docx',
            'invoice_generated_at' => Carbon::create(2025, 8, 30, 14, 30)
        ]);

        $this->command->info("Deuxième paiement créé avec facture: {$payment2->invoice_number}");

        $this->command->newLine();
        $this->command->info('✅ Données de test créées avec succès !');
        $this->command->info('🎯 Vous pouvez maintenant tester les fonctionnalités de paiement et factures.');
    }
}
