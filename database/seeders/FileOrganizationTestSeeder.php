<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Parish;
use App\Models\Category;
use App\Models\Entry;
use Carbon\Carbon;

class FileOrganizationTestSeeder extends Seeder
{
    /**
     * Crée des données de test pour tester l'organisation des fichiers
     */
    public function run(): void
    {
        $this->command->info('🧪 Création de données de test pour l\'organisation des fichiers...');

        // Créer une paroisse de test
        $parish = Parish::create([
            'name' => 'Paroisse Test Organisation',
            'storage_path' => 'C:\ParoCompta\Test',
            'enable_payment_tracking' => true,
            'weekly_payment_amount' => 1000,
        ]);

        // Créer les 4 catégories standard
        $categories = [
            'Autres Recettes',
            'Quêtes Paroisse',
            'Quêtes Stations',
            'Autres Quêtes'
        ];

        $categoryModels = [];
        foreach ($categories as $categoryName) {
            $categoryModels[] = Category::create([
                'name' => $categoryName,
                'parish_id' => $parish->id,
                'fields_json' => [
                    ['name' => 'montant_total', 'label' => 'Montant Total', 'type' => 'number'],
                    ['name' => 'observations', 'label' => 'Observations', 'type' => 'textarea'],
                ]
            ]);
        }

        // Créer un utilisateur de test
        $user = User::create([
            'name' => 'Utilisateur Test',
            'email' => 'test@paroisse.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'parish_id' => $parish->id,
        ]);

        // Créer des entries pour différents trimestres et semaines
        $dates = [
            // Trimestre 1 (Janvier-Mars)
            ['2024-01-08', '2024-01-14'], // Janvier S2
            ['2024-02-05', '2024-02-11'], // Février S1
            ['2024-03-18', '2024-03-24'], // Mars S3

            // Trimestre 2 (Avril-Juin)
            ['2024-04-01', '2024-04-07'], // Avril S1
            ['2024-05-13', '2024-05-19'], // Mai S2
            ['2024-06-24', '2024-06-30'], // Juin S4

            // Trimestre 3 (Juillet-Septembre)
            ['2024-07-08', '2024-07-14'], // Juillet S2
            ['2024-08-19', '2024-08-25'], // Août S3

            // Trimestre 4 (Octobre-Décembre)
            ['2024-10-07', '2024-10-13'], // Octobre S1
            ['2024-12-16', '2024-12-22'], // Décembre S3
        ];

        foreach ($dates as $index => $dateRange) {
            $startDate = Carbon::parse($dateRange[0]);
            $endDate = Carbon::parse($dateRange[1]);
            $category = $categoryModels[$index % count($categoryModels)];

            Entry::create([
                'user_id' => $user->id,
                'parish_id' => $parish->id,
                'category_id' => $category->id,
                'week_label' => 'S' . (floor($startDate->day / 7) + 1),
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'data_json' => [
                    'montant_total' => rand(5000, 25000),
                    'observations' => 'Données de test pour ' . $startDate->locale('fr')->isoFormat('MMMM YYYY'),
                ],
                'generated_file' => null, // Sera généré par FileOrganizer
            ]);
        }

        $this->command->info('✅ Données de test créées :');
        $this->command->info("   - 1 paroisse : {$parish->name}");
        $this->command->info("   - " . count($categoryModels) . " catégories");
        $this->command->info("   - 1 utilisateur : {$user->email}");
        $this->command->info("   - " . count($dates) . " fiches comptables");
        $this->command->newLine();
        $this->command->info('🎯 Vous pouvez maintenant tester l\'organisation des fichiers !');
    }
}
