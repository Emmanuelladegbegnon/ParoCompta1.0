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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Le saisissant
            $table->foreignId('parish_id')->constrained()->onDelete('cascade'); // La paroisse
            $table->string('period'); // ex: "2025-04" pour Avril 2025
            $table->integer('weeks_worked'); // Nombre de semaines saisies dans cette période
            $table->decimal('amount_due', 10, 2); // Montant dû (weeks_worked * weekly_amount)
            $table->decimal('amount_paid', 10, 2)->default(0); // Montant déjà payé
            $table->decimal('amount_received', 10, 2); // Montant de ce paiement spécifique
            $table->date('payment_date'); // Date du paiement
            $table->string('payment_method')->nullable(); // Mode de paiement (espèces, virement, etc.)
            $table->text('notes')->nullable(); // Notes optionnelles
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['user_id', 'period']);
            $table->index(['parish_id', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
