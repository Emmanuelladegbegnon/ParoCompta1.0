<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Entry;
use App\Models\User;
use App\Models\Category;
use Carbon\Carbon;

class TestEntriesSeeder extends Seeder
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

        $categories = Category::where('parish_id', $user->parish_id)->get();
        if ($categories->isEmpty()) {
            $this->command->error('Aucune catégorie trouvée pour cette paroisse');
            return;
        }

        // Créer des entries pour les 3 derniers mois
        $months = [
            Carbon::now()->subMonths(2), // Il y a 2 mois
            Carbon::now()->subMonth(),    // Le mois dernier
            Carbon::now(),                // Ce mois
        ];

        foreach ($months as $month) {
            // Simuler un nombre variable de semaines par mois (4 ou 5)
            $weeksInMonth = rand(4, 5);

            for ($week = 1; $week <= $weeksInMonth; $week++) {
                $category = $categories->random();

                // Calculer les dates de la semaine
                $startDate = $month->copy()->startOfMonth()->addWeeks($week - 1);
                $endDate = $startDate->copy()->addDays(6);

                // S'assurer qu'on ne dépasse pas le mois
                if ($endDate->month !== $startDate->month) {
                    $endDate = $startDate->copy()->endOfMonth();
                }

                Entry::create([
                    'user_id' => $user->id,
                    'parish_id' => $user->parish_id,
                    'category_id' => $category->id,
                    'week_label' => $month->locale('fr')->isoFormat('MMMM') . ' S' . $week,
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'data_json' => [
                        'billets_10000_nombre' => rand(0, 5),
                        'billets_5000_nombre' => rand(0, 10),
                        'billets_2000_nombre' => rand(0, 15),
                        'billets_1000_nombre' => rand(5, 25),
                        'billets_500_nombre' => rand(10, 50),
                        'pieces_500_nombre' => rand(0, 20),
                        'pieces_250_nombre' => rand(0, 30),
                        'pieces_200_nombre' => rand(0, 25),
                        'pieces_100_nombre' => rand(10, 100),
                        'pieces_50_nombre' => rand(20, 200),
                        'pieces_25_nombre' => rand(0, 100),
                        'pieces_10_nombre' => rand(50, 500),
                        'pieces_5_nombre' => rand(100, 1000),
                        'pieces_1_nombre' => rand(200, 2000),
                    ],
                ]);

                $this->command->info("Entry créée: {$month->locale('fr')->isoFormat('MMMM YYYY')} S{$week} - {$category->name}");
            }
        }

        $this->command->info('Entries de test créées avec succès !');
    }
}
