<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Parish;
use App\Models\Category;
use App\Models\Entry;

class FileOrganizer
{
    /**
     * Génère le chemin complet pour un fichier Word selon l'organisation :
     * /storage_path/Trimestre_X/Nom_de_la_categorie/Mois_SemaineX.docx
     */
    public static function generateFilePath(Entry $entry): string
    {
        $parish = $entry->parish;
        $category = $entry->category;
        $startDate = Carbon::parse($entry->start_date);

        // 1. Chemin de base de la paroisse
        $basePath = $parish->getStorageBasePath();

        // 2. Déterminer le trimestre
        $quarter = self::getQuarter($startDate);
        $quarterFolder = "Trimestre_{$quarter}";

        // 3. Nom de la catégorie (nettoyer pour le système de fichiers)
        $categoryFolder = self::sanitizeFileName($category->name);

        // 4. Nom du fichier : Mois_SemaineX.docx
        $fileName = self::generateFileName($entry);

        // 5. Construire le chemin complet
        $fullPath = $basePath . DIRECTORY_SEPARATOR .
                   $quarterFolder . DIRECTORY_SEPARATOR .
                   $categoryFolder . DIRECTORY_SEPARATOR .
                   $fileName;

        return $fullPath;
    }

    /**
     * Génère le chemin du dossier (sans le nom du fichier)
     */
    public static function generateDirectoryPath(Entry $entry): string
    {
        $parish = $entry->parish;
        $category = $entry->category;
        $startDate = Carbon::parse($entry->start_date);

        $basePath = $parish->getStorageBasePath();
        $quarter = self::getQuarter($startDate);
        $quarterFolder = "Trimestre_{$quarter}";
        $categoryFolder = self::sanitizeFileName($category->name);

        return $basePath . DIRECTORY_SEPARATOR .
               $quarterFolder . DIRECTORY_SEPARATOR .
               $categoryFolder;
    }

    /**
     * Crée tous les dossiers nécessaires s'ils n'existent pas
     */
    public static function ensureDirectoryExists(Entry $entry): bool
    {
        $directoryPath = self::generateDirectoryPath($entry);

        if (!is_dir($directoryPath)) {
            return mkdir($directoryPath, 0755, true);
        }

        return true;
    }

    /**
     * Détermine le trimestre basé sur la date
     */
    public static function getQuarter(Carbon $date): int
    {
        $month = $date->month;

        if ($month >= 1 && $month <= 3) {
            return 1; // Janvier-Mars
        } elseif ($month >= 4 && $month <= 6) {
            return 2; // Avril-Juin
        } elseif ($month >= 7 && $month <= 9) {
            return 3; // Juillet-Septembre
        } else {
            return 4; // Octobre-Décembre
        }
    }

    /**
     * Génère le nom du fichier : Mois_SemaineX.docx
     */
    public static function generateFileName(Entry $entry): string
    {
        $startDate = Carbon::parse($entry->start_date);

        // Nom du mois en français
        $monthName = $startDate->locale('fr')->isoFormat('MMMM');
        $monthName = ucfirst($monthName); // Première lettre en majuscule

        // Déterminer le numéro de la semaine dans le mois
        $weekNumber = self::getWeekNumberInMonth($startDate);

        return "{$monthName}_S{$weekNumber}.docx";
    }

    /**
     * Détermine le numéro de la semaine dans le mois (S1, S2, S3, S4)
     */
    public static function getWeekNumberInMonth(Carbon $date): int
    {
        // Calculer la semaine basée sur le jour du mois
        $dayOfMonth = $date->day;

        // Semaine 1: jours 1-7, Semaine 2: jours 8-14, etc.
        $weekNumber = (int) ceil($dayOfMonth / 7);

        // S'assurer que c'est entre 1 et 4
        return max(1, min(4, $weekNumber));
    }

    /**
     * Nettoie un nom pour qu'il soit compatible avec le système de fichiers
     */
    public static function sanitizeFileName(string $name): string
    {
        // Remplacer les caractères non autorisés
        $sanitized = preg_replace('/[<>:"\\/\\\\|?*]/', '_', $name);

        // Supprimer les espaces multiples et les remplacer par un seul
        $sanitized = preg_replace('/\s+/', ' ', $sanitized);

        // Supprimer les espaces en début et fin
        $sanitized = trim($sanitized);

        return $sanitized;
    }

    /**
     * Obtient la structure complète des dossiers pour une paroisse
     */
    public static function getDirectoryStructure(Parish $parish): array
    {
        $basePath = $parish->getStorageBasePath();
        $structure = [];

        if (!is_dir($basePath)) {
            return $structure;
        }

        // Parcourir les trimestres
        for ($quarter = 1; $quarter <= 4; $quarter++) {
            $quarterPath = $basePath . DIRECTORY_SEPARATOR . "Trimestre_{$quarter}";

            if (is_dir($quarterPath)) {
                $structure["Trimestre_{$quarter}"] = [];

                // Parcourir les catégories dans ce trimestre
                $categories = array_diff(scandir($quarterPath), ['.', '..']);

                foreach ($categories as $category) {
                    $categoryPath = $quarterPath . DIRECTORY_SEPARATOR . $category;

                    if (is_dir($categoryPath)) {
                        $files = array_diff(scandir($categoryPath), ['.', '..']);
                        $structure["Trimestre_{$quarter}"][$category] = array_values($files);
                    }
                }
            }
        }

        return $structure;
    }

    /**
     * Obtient des informations détaillées sur un fichier
     */
    public static function getFileInfo(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return [];
        }

        return [
            'path' => $filePath,
            'name' => basename($filePath),
            'size' => filesize($filePath),
            'size_formatted' => self::formatBytes(filesize($filePath)),
            'created_at' => date('Y-m-d H:i:s', filectime($filePath)),
            'modified_at' => date('Y-m-d H:i:s', filemtime($filePath)),
            'extension' => pathinfo($filePath, PATHINFO_EXTENSION),
        ];
    }

    /**
     * Formate la taille en octets en format lisible
     */
    public static function formatBytes(int $size, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, $precision) . ' ' . $units[$i];
    }
}
