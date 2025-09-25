<?php

namespace Database\Seeders;

use App\Models\Parish;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mettre à jour toutes les paroisses avec une configuration par défaut
        Parish::chunk(10, function ($parishes) {
            foreach ($parishes as $parish) {
                $parish->update([
                    'invoice_company_name' => 'ParoCompta Services',
                    'invoice_company_description' => 'Système de Suivi des Recettes et Quêtes Paroissiales',
                    'invoice_company_address' => 'Abidjan, Côte d\'Ivoire',
                    'invoice_company_phone' => '+225 XX XX XX XX',
                    'invoice_company_email' => 'contact@parocompta.local',
                    'invoice_company_ifu' => null,

                    'invoice_parish_address' => 'Adresse de la paroisse ' . $parish->name,
                    'invoice_parish_phone' => '+225 XX XX XX XX',
                    'invoice_parish_contact_name' => 'Responsable ' . $parish->name,
                    'invoice_parish_contact_phone' => '+225 XX XX XX XX',

                    'invoice_payment_method' => 'Espèces',
                    'invoice_legal_mentions' => 'Facture établie selon les normes comptables en vigueur. TVA non applicable - Régime de franchise en base. Application destinée au suivi des recettes et quêtes paroissiales uniquement. Elle ne constitue pas un système de comptabilité paroissiale complète.',
                ]);
            }
        });

        $this->command->info('Configuration des factures mise à jour pour toutes les paroisses.');
    }
}
