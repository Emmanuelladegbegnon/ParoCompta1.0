<?php

namespace App\Support;

class CategoryPresets
{
	public static function autresRecettes(): array
	{
		$fields = [];
		$fields[] = ['name' => 'date', 'label' => 'Date', 'type' => 'date'];
		$fields[] = ['name' => 'numero', 'label' => 'Numéro', 'type' => 'text'];

		$denomsBillets = [10000, 5000, 2000, 1000, 500];
		foreach ($denomsBillets as $amt) {
			$fields[] = ['name' => 'billets_'.$amt.'_nombre', 'label' => 'Billets '.$amt.' - Nombre', 'type' => 'number'];
			$fields[] = ['name' => 'billets_'.$amt.'_montant', 'label' => 'Billets '.$amt.' - Montant', 'type' => 'number'];
		}
		$denomsPieces = [500, 250, 200, 100, 50, 25, 10, 5, 1];
		foreach ($denomsPieces as $amt) {
			$fields[] = ['name' => 'pieces_'.$amt.'_nombre', 'label' => 'Pièces '.$amt.' - Nombre', 'type' => 'number'];
			$fields[] = ['name' => 'pieces_'.$amt.'_montant', 'label' => 'Pièces '.$amt.' - Montant', 'type' => 'number'];
		}

		$fields[] = ['name' => 'total_numeraires', 'label' => 'Total Numéraire', 'type' => 'number'];
		$fields[] = ['name' => 'montant_en_lettres', 'label' => 'Montant en lettres', 'type' => 'text'];

		$labels = [
			'recettes_troncs' => 'Recettes des troncs',
			'denier_culte' => 'Denier de culte',
			'dimes' => 'Dîmes',
			'dons_fonctionnement' => 'Dons reçus pour le fonctionnement',
			'ventes_diverses' => 'Ventes diverses',
			'alimentation_economat' => 'Alimentation reçue de l’Economat',
			'subside_perequation' => 'Subsides de la caisse de péréquation',
			'subside_catechistes' => 'Subsides pour la rémunération des catéchistes',
			'intentions_messe' => 'Intentions de Messe',
			'recettes_catechese' => 'Recettes pour la catéchèse',
			'appui_salaire_cuisinier' => 'Appui pour le salaire du cuisinier',
			'zindo_paroisse' => 'Zindo Paroisse',
			'autres_recettes' => 'Autres recettes',
		];
		foreach ($labels as $name => $label) {
			$fields[] = ['name' => $name, 'label' => $label, 'type' => 'number'];
		}

		return $fields;
	}

	public static function billetsEtPiecesFields(): array
	{
		$fields = [];
		$denomsBillets = [10000, 5000, 2000, 1000, 500];
		foreach ($denomsBillets as $amt) {
			$fields[] = ['name' => 'billets_'.$amt.'_nombre', 'label' => 'Billets '.$amt.' - Nombre', 'type' => 'number', 'unit' => $amt];
			$fields[] = ['name' => 'billets_'.$amt.'_montant', 'label' => 'Billets '.$amt.' - Montant', 'type' => 'number'];
		}
		$denomsPieces = [500, 250, 200, 100, 50, 25, 10, 5, 1];
		foreach ($denomsPieces as $amt) {
			$fields[] = ['name' => 'pieces_'.$amt.'_nombre', 'label' => 'Pièces '.$amt.' - Nombre', 'type' => 'number', 'unit' => $amt];
			$fields[] = ['name' => 'pieces_'.$amt.'_montant', 'label' => 'Pièces '.$amt.' - Montant', 'type' => 'number'];
		}
		$fields[] = ['name' => 'total_numeraires', 'label' => 'Total Numéraire', 'type' => 'number'];
		return $fields;
	}

	public static function quetesParoisse(): array
	{
		$fields = [];
		$fields[] = ['name' => 'date', 'label' => 'Date', 'type' => 'date'];
		$fields[] = ['name' => 'numero', 'label' => 'Numéro', 'type' => 'text'];
		$fields = array_merge($fields, static::billetsEtPiecesFields());
		$rows = [
			['premieres_quetes', 'Premières quêtes'],
			['deuxiemes_quetes', 'Deuxièmes quêtes'],
			['deuxiemes_quetes_funerrailles', 'Deuxièmes quêtes funérailles'],
			['autres_quetes', 'Autres quêtes'],
		];
		foreach ($rows as [$name, $label]) {
			$fields[] = ['name' => $name.'_nombre', 'label' => $label.' - Nombre', 'type' => 'number'];
			$fields[] = ['name' => $name.'_montant', 'label' => $label.' - Montant', 'type' => 'number'];
		}
		$fields[] = ['name' => 'montant_en_lettres', 'label' => 'Montant en lettres', 'type' => 'text'];
		$fields[] = ['name' => 'responsable_1', 'label' => 'Responsable 1 (Nom)', 'type' => 'text'];
		$fields[] = ['name' => 'responsable_2', 'label' => 'Responsable 2 (Nom)', 'type' => 'text'];
		$fields[] = ['name' => 'responsable_3', 'label' => 'Responsable 3 (Nom)', 'type' => 'text'];
		return $fields;
	}

	public static function quetesStations(): array
	{
		$fields = [];
		$fields[] = ['name' => 'date', 'label' => 'Date', 'type' => 'date'];
		$fields[] = ['name' => 'numero', 'label' => 'Numéro', 'type' => 'text'];
		$fields = array_merge($fields, static::billetsEtPiecesFields());
		$rows = [
			['premieres_quetes', 'Premières quêtes'],
			['deuxiemes_quetes', 'Deuxièmes quêtes'],
			['autres_quetes', 'Autres quêtes'],
		];
		foreach ($rows as [$name, $label]) {
			$fields[] = ['name' => $name.'_nombre', 'label' => $label.' - Nombre', 'type' => 'number'];
			$fields[] = ['name' => $name.'_montant', 'label' => $label.' - Montant', 'type' => 'number'];
		}
		$fields[] = ['name' => 'montant_en_lettres', 'label' => 'Montant en lettres', 'type' => 'text'];
		$fields[] = ['name' => 'responsable_1', 'label' => 'Responsable 1 (Nom)', 'type' => 'text'];
		$fields[] = ['name' => 'responsable_2', 'label' => 'Responsable 2 (Nom)', 'type' => 'text'];
		$fields[] = ['name' => 'responsable_3', 'label' => 'Responsable 3 (Nom)', 'type' => 'text'];
		return $fields;
	}

	public static function autresQuetes(): array
	{
		$fields = [];
		$fields[] = ['name' => 'date', 'label' => 'Date', 'type' => 'date'];
		$fields[] = ['name' => 'numero', 'label' => 'Numéro', 'type' => 'text'];
		$fields = array_merge($fields, static::billetsEtPiecesFields());
		$rows = [
			['quetes_semaines', 'Quêtes semaines'],
			['quetes_caritas', 'Quêtes Caritas'],
			['quetes_speciales_1', 'Quêtes spéciales 1'],
			['quetes_speciales_2', 'Quêtes spéciales 2'],
		];
		foreach ($rows as [$name, $label]) {
			$fields[] = ['name' => $name.'_nombre', 'label' => $label.' - Nombre', 'type' => 'number'];
			$fields[] = ['name' => $name.'_montant', 'label' => $label.' - Montant', 'type' => 'number'];
		}
		$fields[] = ['name' => 'montant_en_lettres', 'label' => 'Montant en lettres', 'type' => 'text'];
		$fields[] = ['name' => 'responsable_1', 'label' => 'Responsable 1 (Nom)', 'type' => 'text'];
		$fields[] = ['name' => 'responsable_2', 'label' => 'Responsable 2 (Nom)', 'type' => 'text'];
		$fields[] = ['name' => 'responsable_3', 'label' => 'Responsable 3 (Nom)', 'type' => 'text'];
		return $fields;
	}
}


