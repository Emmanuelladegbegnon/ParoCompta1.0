<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Parish;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    /**
     * Afficher la liste des paiements
     */
    public function index(Request $request): JsonResponse
    {
        $query = Payment::with('parish');
        
        if ($request->has('parish_id')) {
            $query->where('parish_id', $request->parish_id);
        }
        
        $payments = $query->latest()->paginate(15);
        return response()->json($payments);
    }

    /**
     * Enregistrer un nouveau paiement
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'parish_id' => 'required|exists:parishes,id',
            'amount' => 'required|numeric',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $payment = Payment::create($validated);
        return response()->json(['data' => $payment, 'message' => 'Paiement enregistré avec succès'], 201);
    }

    /**
     * Afficher un paiement spécifique
     */
    public function show(Payment $payment): JsonResponse
    {
        $payment->load('parish');
        return response()->json(['data' => $payment]);
    }

    /**
     * Mettre à jour un paiement
     */
    public function update(Request $request, Payment $payment): JsonResponse
    {
        $validated = $request->validate([
            'parish_id' => 'sometimes|exists:parishes,id',
            'amount' => 'sometimes|numeric',
            'payment_date' => 'sometimes|date',
            'notes' => 'nullable|string',
        ]);

        $payment->update($validated);
        return response()->json(['data' => $payment, 'message' => 'Paiement mis à jour avec succès']);
    }

    /**
     * Supprimer un paiement
     */
    public function destroy(Payment $payment): JsonResponse
    {
        $payment->delete();
        return response()->json(['message' => 'Paiement supprimé avec succès']);
    }

    /**
     * Obtenir les statistiques de paiement
     */
    public function stats(Request $request): JsonResponse
    {
        $parishId = $request->input('parish_id');
        
        if ($parishId) {
            $parish = Parish::findOrFail($parishId);
            $totalPayments = Payment::where('parish_id', $parishId)->sum('amount');
            $weeklyAmount = $parish->weekly_payment_amount ?? 0;
            $expectedTotal = $weeklyAmount * date('W'); // Semaine actuelle de l'année
            
            return response()->json([
                'total_payments' => $totalPayments,
                'expected_total' => $expectedTotal,
                'difference' => $totalPayments - $expectedTotal,
                'weekly_amount' => $weeklyAmount
            ]);
        }
        
        // Statistiques globales
        $totalPayments = Payment::sum('amount');
        $parishesCount = Parish::count();
        
        return response()->json([
            'total_payments' => $totalPayments,
            'parishes_count' => $parishesCount,
            'average_per_parish' => $parishesCount > 0 ? $totalPayments / $parishesCount : 0
        ]);
    }
}