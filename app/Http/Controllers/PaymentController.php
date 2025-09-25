<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Models\Parish;
use App\Models\Payment;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Afficher le tableau de bord des paiements
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $year = (int)$request->input('year', now()->year);
        $month = (int)$request->input('month', now()->month);

        // Pour l'admin, permettre la sélection de paroisse
        if ($user->role === 'admin') {
            // Vérifier s'il y a des paroisses
            if (Parish::count() === 0) {
                return view('payments.no-parishes');
            }

            $parishId = $request->input('parish_id');
            if ($parishId) {
                $parish = Parish::findOrFail($parishId);
            } else {
                // Sélectionner la paroisse avec le plus de données (fiches + paiements)
                $parish = Parish::withCount(['entries', 'payments'])
                    ->orderByDesc('entries_count')
                    ->orderByDesc('payments_count')
                    ->first();

                if (!$parish) {
                    return view('payments.no-parishes');
                }
            }
        } else {
            // Pour les utilisateurs normaux, vérifier qu'ils ont une paroisse assignée
            if (!$user->parish) {
                abort(403, 'Aucune paroisse assignée à cet utilisateur.');
            }
            $parish = $user->parish;
        }

        // Vérifier si le suivi des paiements est activé pour cette paroisse
        if (!$parish->enable_payment_tracking) {
            return view('payments.disabled');
        }

        // Calculer les données pour le mois sélectionné
        $period = sprintf('%04d-%02d', $year, $month);

        // Pour l'admin, afficher les données de tous les utilisateurs de la paroisse
        if ($user->role === 'admin') {
            // Obtenir tous les paiements de la paroisse pour ce mois
            $payments = Payment::where('parish_id', $parish->id)
                              ->where('period', $period)
                              ->orderBy('payment_date', 'desc')
                              ->get();

            // Calculer les données agrégées pour la paroisse
            [$year, $month] = explode('-', $period);
            $totalWeeksWorked = Entry::where('parish_id', $parish->id)
                ->whereYear('start_date', $year)
                ->whereMonth('start_date', $month)
                ->count();

            $weeklyAmount = $parish->weekly_payment_amount ?? 0;
            $totalAmountDue = $totalWeeksWorked * $weeklyAmount;

            $monthData = [
                'weeks_worked' => $totalWeeksWorked,
                'amount_due' => $totalAmountDue,
                'weekly_amount' => $weeklyAmount,
            ];

            // Historique des 6 derniers mois pour la paroisse
            $monthsHistory = [];
            for ($i = 0; $i < 6; $i++) {
                $date = now()->subMonths($i);
                $periodKey = $date->format('Y-m');
                [$histYear, $histMonth] = explode('-', $periodKey);

                $weeksWorked = Entry::where('parish_id', $parish->id)
                    ->whereYear('start_date', $histYear)
                    ->whereMonth('start_date', $histMonth)
                    ->count();

                $amountDue = $weeksWorked * $weeklyAmount;
                $amountPaid = Payment::where('parish_id', $parish->id)
                                   ->where('period', $periodKey)
                                   ->sum('amount_received');

                $monthsHistory[] = [
                    'period' => $periodKey,
                    'period_name' => $date->format('F Y'),
                    'weeks_worked' => $weeksWorked,
                    'amount_due' => $amountDue,
                    'amount_paid' => $amountPaid,
                    'remaining_balance' => $amountDue - $amountPaid,
                    'is_fully_paid' => $amountPaid >= $amountDue,
                ];
            }

            // Statistiques cumulées pour la paroisse
            $allPayments = Payment::where('parish_id', $parish->id)->get();
            $totalWeeks = Entry::where('parish_id', $parish->id)->count();
            $totalDue = $totalWeeks * $weeklyAmount;
            $totalReceived = $allPayments->sum('amount_received');

            // Nombre de mois avec des paiements
            $monthsWithPayments = Payment::where('parish_id', $parish->id)
                                        ->distinct('period')
                                        ->count('period');

            // Statistiques par année (compatible SQLite)
            $yearlyStats = Payment::where('parish_id', $parish->id)
                                 ->selectRaw('strftime("%Y", payment_date) as year, SUM(amount_received) as total_received, COUNT(*) as payment_count')
                                 ->groupBy('year')
                                 ->orderBy('year', 'desc')
                                 ->get();

            // Mois avec le plus de paiements
            $bestMonth = Payment::where('parish_id', $parish->id)
                               ->selectRaw('period, SUM(amount_received) as total')
                               ->groupBy('period')
                               ->orderBy('total', 'desc')
                               ->first();

            // Calculs des moyennes
            $averagePerMonth = $monthsWithPayments > 0 ? $totalReceived / $monthsWithPayments : 0;
            $averagePerWeek = $totalWeeks > 0 ? $totalReceived / $totalWeeks : 0;
            $completionRate = $totalDue > 0 ? ($totalReceived / $totalDue) * 100 : 0;

            $cumulativeStats = [
                'total_weeks' => $totalWeeks,
                'total_received' => $totalReceived,
                'total_due' => $totalDue,
                'total_balance' => $totalDue - $totalReceived,
                'average_per_week' => $averagePerWeek,
                'average_per_month' => $averagePerMonth,
                'months_with_payments' => $monthsWithPayments,
                'completion_rate' => $completionRate,
                'yearly_stats' => $yearlyStats,
                'best_month' => $bestMonth,
            ];
        } else {
            $monthData = $this->calculateMonthData($user, $period);

            // Obtenir l'historique des paiements pour ce mois
            $payments = Payment::where('user_id', $user->id)
                              ->where('period', $period)
                              ->orderBy('payment_date', 'desc')
                              ->get();

            $monthsHistory = $this->getMonthsHistory($user, 6);
            $cumulativeStats = $this->getCumulativeStats($user);
        }

        // Calculer le total payé pour ce mois
        $totalPaid = $payments->sum('amount_received');

        // Pour l'admin, récupérer toutes les paroisses pour le sélecteur
        $parishes = $user->role === 'admin' ? Parish::all() : collect([$parish]);

        return view('payments.index', [
            'user' => $user,
            'parish' => $parish,
            'parishes' => $parishes,
            'year' => $year,
            'month' => $month,
            'period' => $period,
            'monthData' => $monthData,
            'payments' => $payments,
            'totalPaid' => $totalPaid,
            'monthsHistory' => $monthsHistory,
            'cumulativeStats' => $cumulativeStats,
        ]);
    }

    /**
     * Enregistrer un nouveau paiement
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'period' => 'required|string|regex:/^\d{4}-\d{2}$/',
            'amount_received' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Calculer les données du mois
        $monthData = $this->calculateMonthData($user, $validated['period']);

        if ($monthData['weeks_worked'] == 0) {
            return back()->withErrors(['period' => 'Aucune fiche saisie pour cette période.']);
        }

        // Calculer le total déjà payé pour cette période
        $totalAlreadyPaid = Payment::where('user_id', $user->id)
                                  ->where('period', $validated['period'])
                                  ->sum('amount_received');

        // Vérifier qu'on ne dépasse pas le montant dû
        if ($totalAlreadyPaid + $validated['amount_received'] > $monthData['amount_due']) {
            return back()->withErrors([
                'amount_received' => 'Le montant total des paiements ne peut pas dépasser le montant dû (' .
                                   number_format($monthData['amount_due'], 0, ',', ' ') . ' F).'
            ]);
        }

        // Créer le paiement
        Payment::create([
            'user_id' => $user->id,
            'parish_id' => $user->parish_id,
            'period' => $validated['period'],
            'weeks_worked' => $monthData['weeks_worked'],
            'amount_due' => $monthData['amount_due'],
            'amount_paid' => $totalAlreadyPaid + $validated['amount_received'],
            'amount_received' => $validated['amount_received'],
            'payment_date' => $validated['payment_date'],
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'],
        ]);

        return back()->with('success', 'Paiement enregistré avec succès !');
    }

    /**
     * Calculer les données d'un mois (semaines travaillées, montant dû)
     */
    private function calculateMonthData($user, string $period): array
    {
        [$year, $month] = explode('-', $period);

        // Compter les fiches saisies par cet utilisateur dans cette période
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
     * Obtenir l'historique des derniers mois
     */
    private function getMonthsHistory($user, int $monthsCount): array
    {
        $history = [];
        $currentDate = now();

        for ($i = 0; $i < $monthsCount; $i++) {
            $date = $currentDate->copy()->subMonths($i);
            $period = $date->format('Y-m');

            $monthData = $this->calculateMonthData($user, $period);
            $totalPaid = Payment::where('user_id', $user->id)
                               ->where('period', $period)
                               ->sum('amount_received');

            $history[] = [
                'period' => $period,
                'period_name' => $date->locale('fr')->isoFormat('MMMM YYYY'),
                'weeks_worked' => $monthData['weeks_worked'],
                'amount_due' => $monthData['amount_due'],
                'amount_paid' => $totalPaid,
                'remaining_balance' => $monthData['amount_due'] - $totalPaid,
                'is_fully_paid' => $totalPaid >= $monthData['amount_due'],
            ];
        }

        return $history;
    }

    /**
     * Obtenir les statistiques cumulées de tous les paiements
     */
    private function getCumulativeStats($user): array
    {
        // Total de tous les paiements reçus
        $totalReceived = Payment::where('user_id', $user->id)->sum('amount_received');

        // Total de tous les montants dus
        $totalDue = Payment::where('user_id', $user->id)
                          ->selectRaw('SUM(DISTINCT amount_due) as total')
                          ->groupBy('period')
                          ->get()
                          ->sum('total');

        // Nombre total de semaines travaillées
        $totalWeeks = Entry::where('user_id', $user->id)->count();

        // Nombre de mois avec des paiements
        $monthsWithPayments = Payment::where('user_id', $user->id)
                                    ->distinct('period')
                                    ->count('period');

        // Statistiques par année (compatible SQLite)
        $yearlyStats = Payment::where('user_id', $user->id)
                             ->selectRaw('strftime("%Y", payment_date) as year, SUM(amount_received) as total_received, COUNT(*) as payment_count')
                             ->groupBy('year')
                             ->orderBy('year', 'desc')
                             ->get();

        // Mois avec le plus de paiements
        $bestMonth = Payment::where('user_id', $user->id)
                           ->selectRaw('period, SUM(amount_received) as total')
                           ->groupBy('period')
                           ->orderBy('total', 'desc')
                           ->first();

        // Calcul du montant moyen par mois
        $averagePerMonth = $monthsWithPayments > 0 ? $totalReceived / $monthsWithPayments : 0;

        // Calcul du montant moyen par semaine
        $averagePerWeek = $totalWeeks > 0 ? $totalReceived / $totalWeeks : 0;

        // Solde total restant
        $totalBalance = $totalDue - $totalReceived;

        return [
            'total_received' => $totalReceived,
            'total_due' => $totalDue,
            'total_balance' => $totalBalance,
            'total_weeks' => $totalWeeks,
            'months_with_payments' => $monthsWithPayments,
            'average_per_month' => $averagePerMonth,
            'average_per_week' => $averagePerWeek,
            'yearly_stats' => $yearlyStats,
            'best_month' => $bestMonth,
            'completion_rate' => $totalDue > 0 ? ($totalReceived / $totalDue) * 100 : 0,
        ];
    }

    /**
     * Générer une facture pour un paiement
     */
    public function generateInvoice(Request $request, Payment $payment): RedirectResponse
    {
        $user = $request->user();

        // Vérifier que l'utilisateur peut accéder à ce paiement
        if ($user->role !== 'admin' && $payment->user_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }

        try {
            $invoiceService = app(InvoiceService::class);
            $filePath = $invoiceService->generateInvoice($payment);

            return back()->with('success', 'Facture générée avec succès ! Numéro: ' . $payment->invoice_number);
        } catch (\Exception $e) {
            return back()->withErrors(['invoice' => 'Erreur lors de la génération de la facture: ' . $e->getMessage()]);
        }
    }

    /**
     * Télécharger la facture d'un paiement
     */
    public function downloadInvoice(Request $request, Payment $payment): BinaryFileResponse
    {
        $user = $request->user();

        // Vérifier que l'utilisateur peut accéder à ce paiement
        if ($user->role !== 'admin' && $payment->user_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }

        if (!$payment->hasInvoice()) {
            abort(404, 'Aucune facture générée pour ce paiement');
        }

        $invoiceService = app(InvoiceService::class);
        $filePath = $invoiceService->getInvoicePath($payment);

        if (!$filePath || !file_exists($filePath)) {
            abort(404, 'Fichier de facture introuvable');
        }

        return response()->download($filePath, $payment->invoice_filename);
    }
}
