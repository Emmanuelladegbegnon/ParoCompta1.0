<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('parishes', function (Blueprint $table) {
            // Informations de l'émetteur (ParoCompta Services)
            $table->string('invoice_company_name')->default('ParoCompta Services');
            $table->text('invoice_company_description')->default('Système de Suivi des Recettes Paroissiales');
            $table->text('invoice_company_address')->nullable();
            $table->string('invoice_company_phone')->nullable();
            $table->string('invoice_company_email')->default('contact@parocompta.local');
            $table->string('invoice_company_ifu')->nullable();

            // Informations du client (paroisse)
            $table->text('invoice_parish_address')->nullable();
            $table->string('invoice_parish_phone')->nullable();
            $table->string('invoice_parish_contact_name')->nullable();
            $table->string('invoice_parish_contact_phone')->nullable();

            // Paramètres de facturation
            $table->string('invoice_payment_method')->default('Espèces');
            $table->text('invoice_legal_mentions')->default('Facture établie selon les normes en vigueur. Application destinée au suivi des recettes et quêtes paroissiales uniquement.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parishes', function (Blueprint $table) {
            $table->dropColumn([
                'invoice_company_name',
                'invoice_company_description',
                'invoice_company_address',
                'invoice_company_phone',
                'invoice_company_email',
                'invoice_company_ifu',
                'invoice_parish_address',
                'invoice_parish_phone',
                'invoice_parish_contact_name',
                'invoice_parish_contact_phone',
                'invoice_payment_method',
                'invoice_legal_mentions'
            ]);
        });
    }
};
