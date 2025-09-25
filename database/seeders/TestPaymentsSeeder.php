<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\User;
use App\Models\Entry;
use Carbon\Carbon;

class TestPaymentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        if (!$user) {
            $this->command->error('Aucun utilisateur trouvé');
            return;
        }

        // Obtenir les entries existantes groupées par mois
        $entriesByMonth = Entry::where('user_id', $user->id)
            ->selectRaw('strftime("%Y-%m", start_date) as period, COUNT(*) as weeks_count')
            ->groupBy('period')
            ->get();

        if ($entriesByMonth->isEmpty()) {
            $this->command->error('Aucune entry trouvée pour cet utilisateur');
            return;
        }

        $weeklyAmount = $user->parish->weekly_payment_amount ?? 1000;

        foreach ($entriesByMonth as $monthData) {
            $period = $monthData->period;
            $weeksWorked = $monthData->weeks_count;
            $amountDue = $weeksWorked * $weeklyAmount;

            // Simuler des paiements partiels pour certains mois
            $paymentScenarios = [
                'full' => 1.0,      // Paiement complet
                'partial' => 0.6,   // Paiement partiel (60%)
                'overpaid' => 1.2,  // Surpaiement (rare)
                'none' => 0.0,      // Aucun paiement
            ];

            $scenario = array_rand($paymentScenarios);
            $paymentRatio = $paymentScenarios[$scenario];

            if ($paymentRatio > 0) {
                $amountReceived = $amountDue * $paymentRatio;

                // Créer 1 à 3 paiements pour ce mois
                $numberOfPayments = rand(1, min(3, ceil($paymentRatio * 2)));
                $remainingAmount = $amountReceived;

                for ($i = 0; $i < $numberOfPayments && $remainingAmount > 0; $i++) {
                    $paymentAmount = $i === $numberOfPayments - 1
                        ? $remainingAmount  // Dernier paiement = le reste
                        : rand(500, min(2000, $remainingAmount));

                    $paymentDate = Carbon::createFromFormat('Y-m', $period)
                        ->addDays(rand(5, 25))  // Paiement entre le 5 et 25 du mois
                        ->addMonth()            // Paiement le mois suivant
                        ->toDateString();

                    Payment::create([
                        'user_id' => $user->id,
                        'parish_id' => $user->parish_id,
                        'period' => $period,
                        'weeks_worked' => $weeksWorked,
                        'amount_due' => $amountDue,
                        'amount_paid' => min($amountReceived, $amountDue), // Ne pas dépasser le montant dû
                        'amount_received' => $paymentAmount,
                        'payment_date' => $paymentDate,
                        'payment_method' => ['Espèces', 'Virement', 'Mobile Money', 'Chèque'][rand(0, 3)],
                        'notes' => $scenario === 'overpaid' ? 'Bonus exceptionnel' : null,
                    ]);

                    $remainingAmount -= $paymentAmount;

                    $this->command->info("Paiement créé: {$period} - {$paymentAmount} F ({$scenario})");
                }
            } else {
                $this->command->info("Aucun paiement pour: {$period} (scénario: {$scenario})");
            }
        }

        $this->command->info('Paiements de test créés avec succès !');
    }
}
