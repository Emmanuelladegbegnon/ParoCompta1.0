<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Parish;

class DefaultCategoriesService
{
    /**
     * Définition des 4 catégories par défaut avec leurs formulaires spécifiques
     */
    public static function getDefaultCategories(): array
    {
        return [
            [
                'name' => 'Autres Recettes',
                'fields_json' => [
                    // Section Billets & Pièces (commune à toutes)
                    ['name' => 'billets_10000', 'label' => 'Billets 10 000 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'billets_5000', 'label' => 'Billets 5 000 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'billets_2000', 'label' => 'Billets 2 000 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'billets_1000', 'label' => 'Billets 1 000 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'billets_500', 'label' => 'Billets 500 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_500', 'label' => 'Pièces 500 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_250', 'label' => 'Pièces 250 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_200', 'label' => 'Pièces 200 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_100', 'label' => 'Pièces 100 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_50', 'label' => 'Pièces 50 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_25', 'label' => 'Pièces 25 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_10', 'label' => 'Pièces 10 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_5', 'label' => 'Pièces 5 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],

                    // Section spécifique Autres Recettes
                    ['name' => 'dons_divers', 'label' => 'Dons divers', 'type' => 'number', 'section' => 'recettes'],
                    ['name' => 'offrandes_speciales', 'label' => 'Offrandes spéciales', 'type' => 'number', 'section' => 'recettes'],
                    ['name' => 'vente_objets', 'label' => 'Vente d\'objets religieux', 'type' => 'number', 'section' => 'recettes'],
                    ['name' => 'ceremonies', 'label' => 'Cérémonies (baptêmes, mariages)', 'type' => 'number', 'section' => 'recettes'],
                    ['name' => 'autres', 'label' => 'Autres recettes', 'type' => 'number', 'section' => 'recettes'],

                    // Totaux
                    ['name' => 'total_billets_pieces', 'label' => 'Total Billets & Pièces', 'type' => 'number', 'readonly' => true, 'section' => 'totaux'],
                    ['name' => 'total_recettes', 'label' => 'Total Recettes', 'type' => 'number', 'readonly' => true, 'section' => 'totaux'],
                    ['name' => 'total_general', 'label' => 'Total Général', 'type' => 'number', 'readonly' => true, 'section' => 'totaux'],
                    ['name' => 'montant_en_lettres', 'label' => 'Montant en lettres', 'type' => 'text', 'section' => 'totaux'],
                ]
            ],
            [
                'name' => 'Quêtes Paroisse',
                'fields_json' => [
                    // Section Billets & Pièces (commune)
                    ['name' => 'billets_10000', 'label' => 'Billets 10 000 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'billets_5000', 'label' => 'Billets 5 000 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'billets_2000', 'label' => 'Billets 2 000 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'billets_1000', 'label' => 'Billets 1 000 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'billets_500', 'label' => 'Billets 500 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_500', 'label' => 'Pièces 500 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_250', 'label' => 'Pièces 250 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_200', 'label' => 'Pièces 200 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_100', 'label' => 'Pièces 100 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_50', 'label' => 'Pièces 50 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_25', 'label' => 'Pièces 25 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_10', 'label' => 'Pièces 10 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_5', 'label' => 'Pièces 5 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],

                    // Section spécifique Quêtes Paroisse
                    ['name' => 'quete_dimanche_matin', 'label' => 'Quête dimanche matin', 'type' => 'number', 'section' => 'quetes'],
                    ['name' => 'quete_dimanche_soir', 'label' => 'Quête dimanche soir', 'type' => 'number', 'section' => 'quetes'],
                    ['name' => 'quete_semaine', 'label' => 'Quêtes en semaine', 'type' => 'number', 'section' => 'quetes'],
                    ['name' => 'quete_speciale', 'label' => 'Quête spéciale', 'type' => 'number', 'section' => 'quetes'],
                    ['name' => 'offrandes_messes', 'label' => 'Offrandes de messes', 'type' => 'number', 'section' => 'quetes'],

                    // Totaux
                    ['name' => 'total_billets_pieces', 'label' => 'Total Billets & Pièces', 'type' => 'number', 'readonly' => true, 'section' => 'totaux'],
                    ['name' => 'total_quetes', 'label' => 'Total Quêtes', 'type' => 'number', 'readonly' => true, 'section' => 'totaux'],
                    ['name' => 'total_general', 'label' => 'Total Général', 'type' => 'number', 'readonly' => true, 'section' => 'totaux'],
                    ['name' => 'montant_en_lettres', 'label' => 'Montant en lettres', 'type' => 'text', 'section' => 'totaux'],
                ]
            ],
            [
                'name' => 'Quêtes Stations',
                'fields_json' => [
                    // Section Billets & Pièces (commune)
                    ['name' => 'billets_10000', 'label' => 'Billets 10 000 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'billets_5000', 'label' => 'Billets 5 000 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'billets_2000', 'label' => 'Billets 2 000 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'billets_1000', 'label' => 'Billets 1 000 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'billets_500', 'label' => 'Billets 500 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_500', 'label' => 'Pièces 500 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_250', 'label' => 'Pièces 250 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_200', 'label' => 'Pièces 200 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_100', 'label' => 'Pièces 100 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_50', 'label' => 'Pièces 50 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_25', 'label' => 'Pièces 25 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_10', 'label' => 'Pièces 10 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_5', 'label' => 'Pièces 5 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],

                    // Section spécifique Quêtes Stations
                    ['name' => 'station_1', 'label' => 'Station 1', 'type' => 'number', 'section' => 'stations'],
                    ['name' => 'station_2', 'label' => 'Station 2', 'type' => 'number', 'section' => 'stations'],
                    ['name' => 'station_3', 'label' => 'Station 3', 'type' => 'number', 'section' => 'stations'],
                    ['name' => 'station_4', 'label' => 'Station 4', 'type' => 'number', 'section' => 'stations'],
                    ['name' => 'chapelles', 'label' => 'Chapelles', 'type' => 'number', 'section' => 'stations'],
                    ['name' => 'autres_stations', 'label' => 'Autres stations', 'type' => 'number', 'section' => 'stations'],

                    // Totaux
                    ['name' => 'total_billets_pieces', 'label' => 'Total Billets & Pièces', 'type' => 'number', 'readonly' => true, 'section' => 'totaux'],
                    ['name' => 'total_stations', 'label' => 'Total Stations', 'type' => 'number', 'readonly' => true, 'section' => 'totaux'],
                    ['name' => 'total_general', 'label' => 'Total Général', 'type' => 'number', 'readonly' => true, 'section' => 'totaux'],
                    ['name' => 'montant_en_lettres', 'label' => 'Montant en lettres', 'type' => 'text', 'section' => 'totaux'],
                ]
            ],
            [
                'name' => 'Autres Quêtes',
                'fields_json' => [
                    // Section Billets & Pièces (commune)
                    ['name' => 'billets_10000', 'label' => 'Billets 10 000 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'billets_5000', 'label' => 'Billets 5 000 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'billets_2000', 'label' => 'Billets 2 000 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'billets_1000', 'label' => 'Billets 1 000 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'billets_500', 'label' => 'Billets 500 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_500', 'label' => 'Pièces 500 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_250', 'label' => 'Pièces 250 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_200', 'label' => 'Pièces 200 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_100', 'label' => 'Pièces 100 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_50', 'label' => 'Pièces 50 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_25', 'label' => 'Pièces 25 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_10', 'label' => 'Pièces 10 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],
                    ['name' => 'pieces_5', 'label' => 'Pièces 5 FCFA', 'type' => 'number', 'section' => 'billets_pieces'],

                    // Section spécifique Autres Quêtes
                    ['name' => 'quete_caritas', 'label' => 'Quête Caritas', 'type' => 'number', 'section' => 'autres_quetes'],
                    ['name' => 'quete_missions', 'label' => 'Quête Missions', 'type' => 'number', 'section' => 'autres_quetes'],
                    ['name' => 'quete_vocations', 'label' => 'Quête Vocations', 'type' => 'number', 'section' => 'autres_quetes'],
                    ['name' => 'quete_pape', 'label' => 'Quête du Pape', 'type' => 'number', 'section' => 'autres_quetes'],
                    ['name' => 'quete_extraordinaire', 'label' => 'Quête extraordinaire', 'type' => 'number', 'section' => 'autres_quetes'],
                    ['name' => 'autres_quetes_spec', 'label' => 'Autres quêtes spéciales', 'type' => 'number', 'section' => 'autres_quetes'],

                    // Totaux
                    ['name' => 'total_billets_pieces', 'label' => 'Total Billets & Pièces', 'type' => 'number', 'readonly' => true, 'section' => 'totaux'],
                    ['name' => 'total_autres_quetes', 'label' => 'Total Autres Quêtes', 'type' => 'number', 'readonly' => true, 'section' => 'totaux'],
                    ['name' => 'total_general', 'label' => 'Total Général', 'type' => 'number', 'readonly' => true, 'section' => 'totaux'],
                    ['name' => 'montant_en_lettres', 'label' => 'Montant en lettres', 'type' => 'text', 'section' => 'totaux'],
                ]
            ]
        ];
    }

    /**
     * Crée les catégories par défaut pour une paroisse
     */
    public static function createDefaultCategoriesForParish(Parish $parish): void
    {
        $defaultCategories = self::getDefaultCategories();

        foreach ($defaultCategories as $categoryData) {
            Category::create([
                'name' => $categoryData['name'],
                'parish_id' => $parish->id,
                'fields_json' => $categoryData['fields_json'],
            ]);
        }
    }

    /**
     * Vérifie si une paroisse a déjà les catégories par défaut
     */
    public static function parishHasDefaultCategories(Parish $parish): bool
    {
        $defaultCategoryNames = collect(self::getDefaultCategories())->pluck('name');
        $existingCategories = $parish->categories()->whereIn('name', $defaultCategoryNames)->count();

        return $existingCategories >= count($defaultCategoryNames);
    }

    /**
     * Obtient les noms des catégories par défaut
     */
    public static function getDefaultCategoryNames(): array
    {
        return collect(self::getDefaultCategories())->pluck('name')->toArray();
    }
}
