<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remettre les champs de configuration des factures à leurs valeurs par défaut
        DB::table('parishes')->update([
            'invoice_company_name' => 'ParoCompta Services',
            'invoice_company_description' => 'Système de Suivi des Recettes Paroissiales',
            'invoice_company_address' => null,
            'invoice_company_phone' => null,
            'invoice_company_email' => 'contact@parocompta.local',
            'invoice_company_ifu' => null,

            'invoice_parish_address' => null,
            'invoice_parish_phone' => null,
            'invoice_parish_contact_name' => null,
            'invoice_parish_contact_phone' => null,

            'invoice_payment_method' => 'Espèces',
            'invoice_legal_mentions' => 'Facture établie selon les normes en vigueur. Application destinée au suivi des recettes et quêtes paroissiales uniquement.',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pas de rollback nécessaire - c'est juste un reset des données
    }
};
