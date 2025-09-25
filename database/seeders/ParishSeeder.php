<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Parish;
use App\Models\Category;

class ParishSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parish = Parish::create([
            'name' => 'Paroisse Saint Michel',
            'enable_payment_tracking' => true,
            'weekly_payment_amount' => 1000,
            'storage_path' => 'public',
        ]);

        // Les 4 catégories fixes selon les spécifications
        $this->createAutresRecettes($parish);
        $this->createQuetesParoisse($parish);
        $this->createQuetesStations($parish);
        $this->createAutresQuetes($parish);
    }

    /**
     * Catégorie 1: Autres Recettes
     */
    private function createAutresRecettes(Parish $parish): void
    {
        $fields = [
            // Billets & Pièces (structure commune)
            ['name' => 'billets_10000_nombre', 'label' => 'Billets 10 000', 'type' => 'number', 'unit' => 10000],
            ['name' => 'billets_10000_montant', 'label' => 'Montant 10 000', 'type' => 'number', 'readonly' => true],
            ['name' => 'billets_5000_nombre', 'label' => 'Billets 5 000', 'type' => 'number', 'unit' => 5000],
            ['name' => 'billets_5000_montant', 'label' => 'Montant 5 000', 'type' => 'number', 'readonly' => true],
            ['name' => 'billets_2000_nombre', 'label' => 'Billets 2 000', 'type' => 'number', 'unit' => 2000],
            ['name' => 'billets_2000_montant', 'label' => 'Montant 2 000', 'type' => 'number', 'readonly' => true],
            ['name' => 'billets_1000_nombre', 'label' => 'Billets 1 000', 'type' => 'number', 'unit' => 1000],
            ['name' => 'billets_1000_montant', 'label' => 'Montant 1 000', 'type' => 'number', 'readonly' => true],
            ['name' => 'billets_500_nombre', 'label' => 'Billets 500', 'type' => 'number', 'unit' => 500],
            ['name' => 'billets_500_montant', 'label' => 'Montant 500', 'type' => 'number', 'readonly' => true],

            ['name' => 'pieces_500_nombre', 'label' => 'Pièces 500', 'type' => 'number', 'unit' => 500],
            ['name' => 'pieces_500_montant', 'label' => 'Montant 500', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_250_nombre', 'label' => 'Pièces 250', 'type' => 'number', 'unit' => 250],
            ['name' => 'pieces_250_montant', 'label' => 'Montant 250', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_200_nombre', 'label' => 'Pièces 200', 'type' => 'number', 'unit' => 200],
            ['name' => 'pieces_200_montant', 'label' => 'Montant 200', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_100_nombre', 'label' => 'Pièces 100', 'type' => 'number', 'unit' => 100],
            ['name' => 'pieces_100_montant', 'label' => 'Montant 100', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_50_nombre', 'label' => 'Pièces 50', 'type' => 'number', 'unit' => 50],
            ['name' => 'pieces_50_montant', 'label' => 'Montant 50', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_25_nombre', 'label' => 'Pièces 25', 'type' => 'number', 'unit' => 25],
            ['name' => 'pieces_25_montant', 'label' => 'Montant 25', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_10_nombre', 'label' => 'Pièces 10', 'type' => 'number', 'unit' => 10],
            ['name' => 'pieces_10_montant', 'label' => 'Montant 10', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_5_nombre', 'label' => 'Pièces 5', 'type' => 'number', 'unit' => 5],
            ['name' => 'pieces_5_montant', 'label' => 'Montant 5', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_1_nombre', 'label' => 'Pièces 1', 'type' => 'number', 'unit' => 1],
            ['name' => 'pieces_1_montant', 'label' => 'Montant 1', 'type' => 'number', 'readonly' => true],

            // Recettes spécifiques aux "Autres Recettes"
            ['name' => 'recettes_troncs_nombre', 'label' => 'Recettes des troncs', 'type' => 'number'],
            ['name' => 'recettes_troncs_montant', 'label' => 'Montant troncs', 'type' => 'number'],
            ['name' => 'dimes_nombre', 'label' => 'Dîmes', 'type' => 'number'],
            ['name' => 'dimes_montant', 'label' => 'Montant dîmes', 'type' => 'number'],
            ['name' => 'intentions_messe_nombre', 'label' => 'Intentions de Messe', 'type' => 'number'],
            ['name' => 'intentions_messe_montant', 'label' => 'Montant intentions', 'type' => 'number'],
            ['name' => 'offrandes_diverses_nombre', 'label' => 'Offrandes diverses', 'type' => 'number'],
            ['name' => 'offrandes_diverses_montant', 'label' => 'Montant offrandes', 'type' => 'number'],

            // Champs communs
            ['name' => 'total_numeraires', 'label' => 'Total Numéraire', 'type' => 'number', 'readonly' => true],
            ['name' => 'montant_en_lettres', 'label' => 'Montant en lettres', 'type' => 'text'],
        ];

        Category::create([
            'parish_id' => $parish->id,
            'name' => 'Autres Recettes',
            'template_file' => 'templates/autres_recettes.docx',
            'fields_json' => $fields,
        ]);
    }

    /**
     * Catégorie 2: Quêtes Paroisse
     */
    private function createQuetesParoisse(Parish $parish): void
    {
        $fields = [
            // Billets & Pièces (structure commune - même que ci-dessus)
            ['name' => 'billets_10000_nombre', 'label' => 'Billets 10 000', 'type' => 'number', 'unit' => 10000],
            ['name' => 'billets_10000_montant', 'label' => 'Montant 10 000', 'type' => 'number', 'readonly' => true],
            ['name' => 'billets_5000_nombre', 'label' => 'Billets 5 000', 'type' => 'number', 'unit' => 5000],
            ['name' => 'billets_5000_montant', 'label' => 'Montant 5 000', 'type' => 'number', 'readonly' => true],
            ['name' => 'billets_2000_nombre', 'label' => 'Billets 2 000', 'type' => 'number', 'unit' => 2000],
            ['name' => 'billets_2000_montant', 'label' => 'Montant 2 000', 'type' => 'number', 'readonly' => true],
            ['name' => 'billets_1000_nombre', 'label' => 'Billets 1 000', 'type' => 'number', 'unit' => 1000],
            ['name' => 'billets_1000_montant', 'label' => 'Montant 1 000', 'type' => 'number', 'readonly' => true],
            ['name' => 'billets_500_nombre', 'label' => 'Billets 500', 'type' => 'number', 'unit' => 500],
            ['name' => 'billets_500_montant', 'label' => 'Montant 500', 'type' => 'number', 'readonly' => true],

            ['name' => 'pieces_500_nombre', 'label' => 'Pièces 500', 'type' => 'number', 'unit' => 500],
            ['name' => 'pieces_500_montant', 'label' => 'Montant 500', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_250_nombre', 'label' => 'Pièces 250', 'type' => 'number', 'unit' => 250],
            ['name' => 'pieces_250_montant', 'label' => 'Montant 250', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_200_nombre', 'label' => 'Pièces 200', 'type' => 'number', 'unit' => 200],
            ['name' => 'pieces_200_montant', 'label' => 'Montant 200', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_100_nombre', 'label' => 'Pièces 100', 'type' => 'number', 'unit' => 100],
            ['name' => 'pieces_100_montant', 'label' => 'Montant 100', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_50_nombre', 'label' => 'Pièces 50', 'type' => 'number', 'unit' => 50],
            ['name' => 'pieces_50_montant', 'label' => 'Montant 50', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_25_nombre', 'label' => 'Pièces 25', 'type' => 'number', 'unit' => 25],
            ['name' => 'pieces_25_montant', 'label' => 'Montant 25', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_10_nombre', 'label' => 'Pièces 10', 'type' => 'number', 'unit' => 10],
            ['name' => 'pieces_10_montant', 'label' => 'Montant 10', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_5_nombre', 'label' => 'Pièces 5', 'type' => 'number', 'unit' => 5],
            ['name' => 'pieces_5_montant', 'label' => 'Montant 5', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_1_nombre', 'label' => 'Pièces 1', 'type' => 'number', 'unit' => 1],
            ['name' => 'pieces_1_montant', 'label' => 'Montant 1', 'type' => 'number', 'readonly' => true],

            // Recettes spécifiques aux "Quêtes Paroisse"
            ['name' => 'premieres_quetes_nombre', 'label' => 'Premières quêtes', 'type' => 'number'],
            ['name' => 'premieres_quetes_montant', 'label' => 'Montant premières quêtes', 'type' => 'number'],
            ['name' => 'deuxiemes_quetes_nombre', 'label' => 'Deuxièmes quêtes', 'type' => 'number'],
            ['name' => 'deuxiemes_quetes_montant', 'label' => 'Montant deuxièmes quêtes', 'type' => 'number'],
            ['name' => 'quetes_dominicales_nombre', 'label' => 'Quêtes dominicales', 'type' => 'number'],
            ['name' => 'quetes_dominicales_montant', 'label' => 'Montant quêtes dominicales', 'type' => 'number'],

            // Champs communs
            ['name' => 'total_numeraires', 'label' => 'Total Numéraire', 'type' => 'number', 'readonly' => true],
            ['name' => 'montant_en_lettres', 'label' => 'Montant en lettres', 'type' => 'text'],
        ];

        Category::create([
            'parish_id' => $parish->id,
            'name' => 'Quêtes Paroisse',
            'template_file' => 'templates/quetes_paroisse.docx',
            'fields_json' => $fields,
        ]);
    }

    /**
     * Catégorie 3: Quêtes Stations
     */
    private function createQuetesStations(Parish $parish): void
    {
        $fields = [
            // Billets & Pièces (structure commune)
            ['name' => 'billets_10000_nombre', 'label' => 'Billets 10 000', 'type' => 'number', 'unit' => 10000],
            ['name' => 'billets_10000_montant', 'label' => 'Montant 10 000', 'type' => 'number', 'readonly' => true],
            ['name' => 'billets_5000_nombre', 'label' => 'Billets 5 000', 'type' => 'number', 'unit' => 5000],
            ['name' => 'billets_5000_montant', 'label' => 'Montant 5 000', 'type' => 'number', 'readonly' => true],
            ['name' => 'billets_2000_nombre', 'label' => 'Billets 2 000', 'type' => 'number', 'unit' => 2000],
            ['name' => 'billets_2000_montant', 'label' => 'Montant 2 000', 'type' => 'number', 'readonly' => true],
            ['name' => 'billets_1000_nombre', 'label' => 'Billets 1 000', 'type' => 'number', 'unit' => 1000],
            ['name' => 'billets_1000_montant', 'label' => 'Montant 1 000', 'type' => 'number', 'readonly' => true],
            ['name' => 'billets_500_nombre', 'label' => 'Billets 500', 'type' => 'number', 'unit' => 500],
            ['name' => 'billets_500_montant', 'label' => 'Montant 500', 'type' => 'number', 'readonly' => true],

            ['name' => 'pieces_500_nombre', 'label' => 'Pièces 500', 'type' => 'number', 'unit' => 500],
            ['name' => 'pieces_500_montant', 'label' => 'Montant 500', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_250_nombre', 'label' => 'Pièces 250', 'type' => 'number', 'unit' => 250],
            ['name' => 'pieces_250_montant', 'label' => 'Montant 250', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_200_nombre', 'label' => 'Pièces 200', 'type' => 'number', 'unit' => 200],
            ['name' => 'pieces_200_montant', 'label' => 'Montant 200', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_100_nombre', 'label' => 'Pièces 100', 'type' => 'number', 'unit' => 100],
            ['name' => 'pieces_100_montant', 'label' => 'Montant 100', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_50_nombre', 'label' => 'Pièces 50', 'type' => 'number', 'unit' => 50],
            ['name' => 'pieces_50_montant', 'label' => 'Montant 50', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_25_nombre', 'label' => 'Pièces 25', 'type' => 'number', 'unit' => 25],
            ['name' => 'pieces_25_montant', 'label' => 'Montant 25', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_10_nombre', 'label' => 'Pièces 10', 'type' => 'number', 'unit' => 10],
            ['name' => 'pieces_10_montant', 'label' => 'Montant 10', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_5_nombre', 'label' => 'Pièces 5', 'type' => 'number', 'unit' => 5],
            ['name' => 'pieces_5_montant', 'label' => 'Montant 5', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_1_nombre', 'label' => 'Pièces 1', 'type' => 'number', 'unit' => 1],
            ['name' => 'pieces_1_montant', 'label' => 'Montant 1', 'type' => 'number', 'readonly' => true],

            // Recettes spécifiques aux "Quêtes Stations"
            ['name' => 'premieres_quetes_stations_nombre', 'label' => 'Premières quêtes stations', 'type' => 'number'],
            ['name' => 'premieres_quetes_stations_montant', 'label' => 'Montant premières quêtes stations', 'type' => 'number'],
            ['name' => 'deuxiemes_quetes_stations_nombre', 'label' => 'Deuxièmes quêtes stations', 'type' => 'number'],
            ['name' => 'deuxiemes_quetes_stations_montant', 'label' => 'Montant deuxièmes quêtes stations', 'type' => 'number'],
            ['name' => 'autres_quetes_stations_nombre', 'label' => 'Autres quêtes stations', 'type' => 'number'],
            ['name' => 'autres_quetes_stations_montant', 'label' => 'Montant autres quêtes stations', 'type' => 'number'],

            // Champs communs
            ['name' => 'total_numeraires', 'label' => 'Total Numéraire', 'type' => 'number', 'readonly' => true],
            ['name' => 'montant_en_lettres', 'label' => 'Montant en lettres', 'type' => 'text'],
        ];

        Category::create([
            'parish_id' => $parish->id,
            'name' => 'Quêtes Stations',
            'template_file' => 'templates/quetes_stations.docx',
            'fields_json' => $fields,
        ]);
    }

    /**
     * Catégorie 4: Autres Quêtes
     */
    private function createAutresQuetes(Parish $parish): void
    {
        $fields = [
            // Billets & Pièces (structure commune)
            ['name' => 'billets_10000_nombre', 'label' => 'Billets 10 000', 'type' => 'number', 'unit' => 10000],
            ['name' => 'billets_10000_montant', 'label' => 'Montant 10 000', 'type' => 'number', 'readonly' => true],
            ['name' => 'billets_5000_nombre', 'label' => 'Billets 5 000', 'type' => 'number', 'unit' => 5000],
            ['name' => 'billets_5000_montant', 'label' => 'Montant 5 000', 'type' => 'number', 'readonly' => true],
            ['name' => 'billets_2000_nombre', 'label' => 'Billets 2 000', 'type' => 'number', 'unit' => 2000],
            ['name' => 'billets_2000_montant', 'label' => 'Montant 2 000', 'type' => 'number', 'readonly' => true],
            ['name' => 'billets_1000_nombre', 'label' => 'Billets 1 000', 'type' => 'number', 'unit' => 1000],
            ['name' => 'billets_1000_montant', 'label' => 'Montant 1 000', 'type' => 'number', 'readonly' => true],
            ['name' => 'billets_500_nombre', 'label' => 'Billets 500', 'type' => 'number', 'unit' => 500],
            ['name' => 'billets_500_montant', 'label' => 'Montant 500', 'type' => 'number', 'readonly' => true],

            ['name' => 'pieces_500_nombre', 'label' => 'Pièces 500', 'type' => 'number', 'unit' => 500],
            ['name' => 'pieces_500_montant', 'label' => 'Montant 500', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_250_nombre', 'label' => 'Pièces 250', 'type' => 'number', 'unit' => 250],
            ['name' => 'pieces_250_montant', 'label' => 'Montant 250', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_200_nombre', 'label' => 'Pièces 200', 'type' => 'number', 'unit' => 200],
            ['name' => 'pieces_200_montant', 'label' => 'Montant 200', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_100_nombre', 'label' => 'Pièces 100', 'type' => 'number', 'unit' => 100],
            ['name' => 'pieces_100_montant', 'label' => 'Montant 100', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_50_nombre', 'label' => 'Pièces 50', 'type' => 'number', 'unit' => 50],
            ['name' => 'pieces_50_montant', 'label' => 'Montant 50', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_25_nombre', 'label' => 'Pièces 25', 'type' => 'number', 'unit' => 25],
            ['name' => 'pieces_25_montant', 'label' => 'Montant 25', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_10_nombre', 'label' => 'Pièces 10', 'type' => 'number', 'unit' => 10],
            ['name' => 'pieces_10_montant', 'label' => 'Montant 10', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_5_nombre', 'label' => 'Pièces 5', 'type' => 'number', 'unit' => 5],
            ['name' => 'pieces_5_montant', 'label' => 'Montant 5', 'type' => 'number', 'readonly' => true],
            ['name' => 'pieces_1_nombre', 'label' => 'Pièces 1', 'type' => 'number', 'unit' => 1],
            ['name' => 'pieces_1_montant', 'label' => 'Montant 1', 'type' => 'number', 'readonly' => true],

            // Recettes spécifiques aux "Autres Quêtes"
            ['name' => 'quetes_semaines_nombre', 'label' => 'Quêtes semaines', 'type' => 'number'],
            ['name' => 'quetes_semaines_montant', 'label' => 'Montant quêtes semaines', 'type' => 'number'],
            ['name' => 'caritas_nombre', 'label' => 'Caritas', 'type' => 'number'],
            ['name' => 'caritas_montant', 'label' => 'Montant Caritas', 'type' => 'number'],
            ['name' => 'quetes_speciales_nombre', 'label' => 'Quêtes spéciales', 'type' => 'number'],
            ['name' => 'quetes_speciales_montant', 'label' => 'Montant quêtes spéciales', 'type' => 'number'],
            ['name' => 'autres_dons_nombre', 'label' => 'Autres dons', 'type' => 'number'],
            ['name' => 'autres_dons_montant', 'label' => 'Montant autres dons', 'type' => 'number'],

            // Champs communs
            ['name' => 'total_numeraires', 'label' => 'Total Numéraire', 'type' => 'number', 'readonly' => true],
            ['name' => 'montant_en_lettres', 'label' => 'Montant en lettres', 'type' => 'text'],
        ];

        Category::create([
            'parish_id' => $parish->id,
            'name' => 'Autres Quêtes',
            'template_file' => 'templates/autres_quetes.docx',
            'fields_json' => $fields,
        ]);
    }
}
