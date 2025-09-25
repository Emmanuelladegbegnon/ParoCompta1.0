<?php

namespace App\Services;

class TemplateVariableExtractor
{
	/**
	 * Extract variable placeholders from a .docx template.
	 * Supports PhpWord (${var}) and blade-like ({{var}}) syntaxes.
	 *
	 * @param string $absoluteDocxPath Absolute filesystem path to the .docx file
	 * @return array<int,string> Unique variable names
	 */
	public static function extractVariables(string $absoluteDocxPath): array
	{
		$zip = new \ZipArchive();
		if ($zip->open($absoluteDocxPath) !== true) {
			return [];
		}
        $xml = '';
		$index = $zip->locateName('word/document.xml', \ZipArchive::FL_NODIR);
		if ($index !== false) {
			$xml = (string) $zip->getFromIndex($index);
		}
		$zip->close();
        if ($xml === '') {
			return [];
		}

        // Concatenate text by removing XML tags to rebuild placeholders broken across runs
        $plain = strip_tags($xml);
        // Normalize whitespace
        $plain = preg_replace('/\s+/u', ' ', (string) $plain) ?? '';

        $names = [];
        $patterns = [
            '/\$\{\s*([A-Za-z0-9_\.]+)\s*\}/u', // ${var}
            '/\{\{\s*([A-Za-z0-9_\.]+)\s*\}\}/u', // {{var}}
        ];
        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $plain, $matches)) {
                foreach ($matches[1] as $name) {
                    $names[] = (string) $name;
                }
            }
        }

        // Fallback inference when no explicit placeholders found
        if (count($names) === 0) {
            $inferred = [];

            if (stripos($plain, 'date') !== false) {
                $inferred[] = 'date';
            }
            if (preg_match('/N°\s*\d+/u', $plain) || stripos($plain, 'N°') !== false) {
                $inferred[] = 'numero';
            }

            if (preg_match_all('/Billets\s+de\s+(\d{1,5})\s*F/iu', $plain, $m1)) {
                foreach ($m1[1] as $amt) {
                    $inferred[] = 'billets_' . $amt . '_nombre';
                    $inferred[] = 'billets_' . $amt . '_montant';
                }
            }
            if (preg_match_all('/Pi[eè]ces\s+de\s+(\d{1,5})\s*F/iu', $plain, $m2)) {
                foreach ($m2[1] as $amt) {
                    $inferred[] = 'pieces_' . $amt . '_nombre';
                    $inferred[] = 'pieces_' . $amt . '_montant';
                }
            }

            if (stripos($plain, 'Total Numéraire') !== false || stripos($plain, 'Total Numeraire') !== false) {
                $inferred[] = 'total_numeraires';
            }
            if (stripos($plain, 'Montant en lettres') !== false) {
                $inferred[] = 'montant_en_lettres';
            }

            $mapLabels = [
                'Recettes des troncs' => 'recettes_troncs',
                'Denier de culte' => 'denier_culte',
                'Dîmes' => 'dimes',
                'Dons reçus pour le fonctionnement' => 'dons_fonctionnement',
                'Ventes diverses' => 'ventes_diverses',
                'Alimentation reçue de l’Economat' => 'alimentation_economat',
                'Subsides de la caisse de péréquation' => 'subside_perequation',
                'Subsides pour la rémunération des catéchistes' => 'subside_catechistes',
                'Intentions de Messe' => 'intentions_messe',
                'Recettes pour la catéchèse' => 'recettes_catechese',
                'Appui pour le salaire du cuisinier' => 'appui_salaire_cuisinier',
                'Zindo Paroisse' => 'zindo_paroisse',
                'Autres recettes' => 'autres_recettes',
            ];
            foreach ($mapLabels as $label => $var) {
                if (stripos($plain, $label) !== false) {
                    $inferred[] = $var;
                }
            }

            $names = $inferred;
        }

        $names = array_values(array_unique($names));

        return $names;
	}
}


