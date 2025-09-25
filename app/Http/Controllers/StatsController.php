<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Entry;
use Carbon\Carbon;

class StatsController extends Controller
{
    public function index()
    {
        $user = request()->user();

        // Statistiques générales
        $totalStats = $this->getTotalStats($user);

        // Statistiques mensuelles détaillées
        $monthlyStats = $this->getMonthlyStats($user);

        // Évolution des paiements
        $paymentEvolution = $this->getPaymentEvolution($user);

        // Comparaison avec les objectifs
        $goalComparison = $this->getGoalComparison($user);

        return view('stats.index', compact(
            'user',
            'totalStats',
            'monthlyStats',
            'paymentEvolution',
            'goalComparison'
        ));
    }

    private function getTotalStats($user): array
    {
        $totalReceived = Payment::where('user_id', $user->id)->sum('amount_received');
        $totalDue = Payment::where('user_id', $user->id)
                          ->selectRaw('SUM(DISTINCT amount_due) as total')
                          ->groupBy('period')
                          ->get()
                          ->sum('total');

        $totalWeeks = Entry::where('user_id', $user->id)->count();
        $firstEntry = Entry::where('user_id', $user->id)->orderBy('start_date')->first();
        $lastEntry = Entry::where('user_id', $user->id)->orderBy('start_date', 'desc')->first();

        $workingMonths = 0;
        if ($firstEntry && $lastEntry) {
            $start = Carbon::parse($firstEntry->start_date);
            $end = Carbon::parse($lastEntry->start_date);
            $workingMonths = $start->diffInMonths($end) + 1;
        }

        return [
            'total_received' => $totalReceived,
            'total_due' => $totalDue,
            'total_balance' => $totalDue - $totalReceived,
            'total_weeks' => $totalWeeks,
            'working_months' => $workingMonths,
            'average_per_week' => $totalWeeks > 0 ? $totalReceived / $totalWeeks : 0,
            'average_per_month' => $workingMonths > 0 ? $totalReceived / $workingMonths : 0,
            'completion_rate' => $totalDue > 0 ? ($totalReceived / $totalDue) * 100 : 0,
            'first_work_date' => $firstEntry?->start_date,
            'last_work_date' => $lastEntry?->start_date,
        ];
    }

    private function getMonthlyStats($user): array
    {
        return Payment::where('user_id', $user->id)
            ->selectRaw('
                period,
                weeks_worked,
                amount_due,
                SUM(amount_received) as total_received,
                COUNT(*) as payment_count,
                MIN(payment_date) as first_payment,
                MAX(payment_date) as last_payment
            ')
            ->groupBy('period', 'weeks_worked', 'amount_due')
            ->orderBy('period', 'desc')
            ->get()
            ->map(function ($item) {
                $item->balance = $item->amount_due - $item->total_received;
                $item->completion_rate = $item->amount_due > 0 ? ($item->total_received / $item->amount_due) * 100 : 0;
                return $item;
            })
            ->toArray();
    }

    private function getPaymentEvolution($user): array
    {
        $payments = Payment::where('user_id', $user->id)
            ->orderBy('payment_date')
            ->get();

        $evolution = [];
        $cumulativeTotal = 0;

        foreach ($payments as $payment) {
            $cumulativeTotal += $payment->amount_received;
            $evolution[] = [
                'date' => $payment->payment_date,
                'amount' => $payment->amount_received,
                'cumulative' => $cumulativeTotal,
                'period' => $payment->period,
                'method' => $payment->payment_method,
            ];
        }

        return $evolution;
    }

    private function getGoalComparison($user): array
    {
        $weeklyAmount = $user->parish->weekly_payment_amount ?? 1000;
        $currentYear = Carbon::now()->year;

        // Objectif annuel basé sur 52 semaines
        $yearlyGoal = $weeklyAmount * 52;

        // Paiements reçus cette année (compatible SQLite)
        $yearlyReceived = Payment::where('user_id', $user->id)
            ->whereRaw('strftime("%Y", payment_date) = ?', [$currentYear])
            ->sum('amount_received');

        // Semaines travaillées cette année (compatible SQLite)
        $weeksWorkedThisYear = Entry::where('user_id', $user->id)
            ->whereRaw('strftime("%Y", start_date) = ?', [$currentYear])
            ->count();

        // Objectif ajusté selon les semaines réellement travaillées
        $adjustedGoal = $weeklyAmount * $weeksWorkedThisYear;

        return [
            'yearly_goal' => $yearlyGoal,
            'adjusted_goal' => $adjustedGoal,
            'yearly_received' => $yearlyReceived,
            'weeks_worked_this_year' => $weeksWorkedThisYear,
            'goal_completion' => $adjustedGoal > 0 ? ($yearlyReceived / $adjustedGoal) * 100 : 0,
            'remaining_to_goal' => max(0, $adjustedGoal - $yearlyReceived),
        ];
    }
}
