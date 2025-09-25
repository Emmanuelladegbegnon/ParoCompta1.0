<?php

namespace App\Services;

use App\Models\Entry;
use App\Models\Category;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Storage;

class WordDocumentService
{
    /**
     * Générer un document Word à partir d'une entrée
     *
     * @param Entry $entry
     * @param Category $category
     * @return string Le chemin du fichier généré
     */
    public function generateDocument(Entry $entry, Category $category): string
    {
        $templatePath = storage_path('app/' . $category->template_file);
        $processor = new TemplateProcessor($templatePath);

        $data = is_array($entry->data_json) ? $entry->data_json : (json_decode((string) $entry->data_json, true) ?: []);
        foreach ($data as $key => $value) {
            $processor->setValue($key, (string) $value);
        }

        $start = $entry->start_date ? \Carbon\Carbon::parse($entry->start_date) : now();
        $monthName = ucfirst($start->locale('fr')->translatedFormat('F'));
        $quarter = 'trimestre_' . (int)ceil($start->quarter);
        $categoryFolder = 'categorie_' . $category->id;

        $base = trim($entry->parish->storage_path ?: 'public', '/');
        $dir = $base . '/paroisse_' . $entry->parish_id . '/' . $quarter . '/' . $categoryFolder;
        Storage::makeDirectory($dir);

        $filename = str_replace(' ', '_', $monthName . '_' . ($entry->week_label ?? 'Semaine')) . '.docx';
        $relativePath = $dir . '/' . $filename;

        $tempFile = tempnam(sys_get_temp_dir(), 'pc_') . '.docx';
        $processor->saveAs($tempFile);
        Storage::put($relativePath, file_get_contents($tempFile));

        return $relativePath;
    }

    /**
     * Generate a Word document from preset metadata (no template_file).
     * Builds header, two tables, and signatures based on category name and entry data_json.
     */
    public function generateFromPreset(Entry $entry, Category $category): string
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Header
        $title = $category->name;
        $section->addText($title, ['bold' => true, 'size' => 14]);
        $section->addText("Date: " . optional($entry->start_date)->format('d/m/Y') . "    N°: " . ($entry->data_json['numero'] ?? ''), ['size' => 12]);

        $data = is_array($entry->data_json) ? $entry->data_json : (json_decode((string) $entry->data_json, true) ?: []);

        // Table 1: Billets & Pièces
        $table1 = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
        $table1->addRow();
        $table1->addCell(5000)->addText('Type', ['bold' => true]);
        $table1->addCell(2000)->addText('Nombre', ['bold' => true]);
        $table1->addCell(2000)->addText('Montant', ['bold' => true]);

        $addRow = function($label, $base) use ($table1, $data) {
            $nombre = $data[$base . '_nombre'] ?? '';
            $montant = $data[$base . '_montant'] ?? '';
            $table1->addRow();
            $table1->addCell(5000)->addText($label);
            $table1->addCell(2000)->addText((string)$nombre);
            $table1->addCell(2000)->addText((string)$montant);
        };

        // Detect rows for billets/pieces from data keys
        $bases = [];
        foreach (array_keys($data) as $key) {
            if (preg_match('/^(billets|pieces)_(\d+)_montant$/', $key, $m)) {
                $bases[$m[1] . '_' . $m[2]] = ucfirst($m[1]) . ' ' . $m[2];
            }
        }
        ksort($bases, SORT_NATURAL);
        foreach ($bases as $base => $label) {
            $addRow($label, $base);
        }

        // Total numéraire
        if (isset($data['total_numeraires'])) {
            $table1->addRow();
            $table1->addCell(5000)->addText('Total Numéraire', ['bold' => true]);
            $table1->addCell(2000)->addText('');
            $table1->addCell(2000)->addText((string)$data['total_numeraires'], ['bold' => true]);
        }

        $section->addTextBreak(1);

        // Table 2: other lines ending with _montant (excluding billets/pieces)
        $table2 = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
        $table2->addRow();
        $table2->addCell(5000)->addText('Libellé', ['bold' => true]);
        $table2->addCell(2000)->addText('Nombre', ['bold' => true]);
        $table2->addCell(2000)->addText('Montant', ['bold' => true]);

        foreach ($data as $key => $value) {
            if (preg_match('/^(billets|pieces)_/i', $key)) continue;
            if (!str_ends_with($key, '_montant')) continue;
            $base = substr($key, 0, -8); // remove _montant
            $label = ucwords(str_replace('_', ' ', $base));
            $nombre = $data[$base . '_nombre'] ?? '';
            $montant = $data[$base . '_montant'] ?? '';
            $table2->addRow();
            $table2->addCell(5000)->addText($label);
            $table2->addCell(2000)->addText((string)$nombre);
            $table2->addCell(2000)->addText((string)$montant);
        }

        // Montant en lettres
        if (!empty($data['montant_en_lettres'])) {
            $section->addTextBreak(1);
            $section->addText('Montant en lettres: ' . $data['montant_en_lettres']);
        }

        $section->addTextBreak(2);

        // Signatures
        $signTable = $section->addTable(['borderSize' => 0]);
        if (stripos($category->name, 'autres recettes') !== false) {
            $signTable->addRow();
            $signTable->addCell(5000)->addText('Caissier');
            $signTable->addCell(5000)->addText('Visa Curé');
        } else {
            $signTable->addRow();
            $signTable->addCell(3333)->addText('Responsable 1');
            $signTable->addCell(3333)->addText('Responsable 2');
            $signTable->addCell(3333)->addText('Responsable 3');
        }

        // Save
        $start = $entry->start_date ? \Carbon\Carbon::parse($entry->start_date) : now();
        $monthName = ucfirst($start->locale('fr')->translatedFormat('F'));
        $quarter = 'trimestre_' . (int)ceil($start->quarter);
        $categoryFolder = 'categorie_' . $category->id;
        $base = trim($entry->parish->getStorageBasePath(), '/');
        $dir = $base . '/paroisse_' . $entry->parish_id . '/' . $quarter . '/' . $categoryFolder;
        if ($entry->parish->isAbsoluteStorageBase()) {
            if (!is_dir($dir)) { @mkdir($dir, 0777, true); }
        } else {
            \Storage::makeDirectory($dir);
        }
        $filename = str_replace(' ', '_', $monthName . '_' . ($entry->week_label ?? 'Semaine')) . '.docx';
        $relativePath = $dir . '/' . $filename;

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        if ($entry->parish->isAbsoluteStorageBase()) {
            $writer->save($relativePath);
        } else {
            $tmp = tempnam(sys_get_temp_dir(), 'pc_') . '.docx';
            $writer->save($tmp);
            \Storage::put($relativePath, file_get_contents($tmp));
        }

        return $relativePath;
    }
}