<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'parish_id',
        'period',
        'weeks_worked',
        'amount_due',
        'amount_paid',
        'amount_received',
        'payment_date',
        'payment_method',
        'notes',
        'invoice_number',
        'invoice_file',
        'invoice_generated_at',
    ];

    protected $casts = [
        'amount_due' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_received' => 'decimal:2',
        'payment_date' => 'date',
        'invoice_generated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parish(): BelongsTo
    {
        return $this->belongsTo(Parish::class);
    }

    /**
     * Calculer le solde restant à payer
     */
    public function getRemainingBalanceAttribute(): float
    {
        return $this->amount_due - $this->amount_paid;
    }

    /**
     * Vérifier si le paiement est complet
     */
    public function getIsFullyPaidAttribute(): bool
    {
        return $this->amount_paid >= $this->amount_due;
    }

    /**
     * Obtenir le nom du mois en français
     */
    public function getPeriodNameAttribute(): string
    {
        $date = Carbon::createFromFormat('Y-m', $this->period);
        return $date->locale('fr')->isoFormat('MMMM YYYY');
    }

    /**
     * Calculer le montant dû pour une période donnée
     */
    public static function calculateAmountDue(User $user, string $period): array
    {
        // Extraire l'année et le mois de la période (format: "2025-04")
        [$year, $month] = explode('-', $period);

        // Compter les fiches saisies dans cette période
        $weeksWorked = Entry::where('user_id', $user->id)
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', $month)
            ->count();

        // Calculer le montant dû
        $weeklyAmount = $user->parish->weekly_payment_amount ?? 0;
        $amountDue = $weeksWorked * $weeklyAmount;

        return [
            'weeks_worked' => $weeksWorked,
            'amount_due' => $amountDue,
            'weekly_amount' => $weeklyAmount,
        ];
    }

    /**
     * Vérifier si le paiement a une facture générée
     */
    public function hasInvoice(): bool
    {
        return !empty($this->invoice_number) && !empty($this->invoice_file);
    }

    /**
     * Obtenir le nom du fichier de facture
     */
    public function getInvoiceFilenameAttribute(): string
    {
        if (!$this->hasInvoice()) {
            return '';
        }

        return 'Facture_' . $this->invoice_number . '.docx';
    }
}
