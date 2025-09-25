<?php

namespace App\Http\Controllers;

use App\Models\Parish;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ParishConfigController extends Controller
{
    /**
     * Afficher la page de configuration des factures pour une paroisse
     */
    public function invoiceConfig(Request $request): View
    {
        $user = $request->user();

        // Pour l'admin, permettre la sélection de paroisse
        if ($user->role === 'admin') {
            // Vérifier s'il y a des paroisses
            if (Parish::count() === 0) {
                return view('config.no-parishes');
            }

            $parishId = $request->input('parish_id');
            if ($parishId) {
                $parish = Parish::findOrFail($parishId);
            } else {
                // Sélectionner la paroisse avec le plus de données
                $parish = Parish::withCount(['entries', 'payments'])
                    ->orderByDesc('entries_count')
                    ->orderByDesc('payments_count')
                    ->first();

                if (!$parish) {
                    return view('config.no-parishes');
                }
            }

            $parishes = Parish::all();
        } else {
            // Utilisateur normal : sa paroisse uniquement
            if (!$user->parish_id) {
                return view('config.no-parish-assigned');
            }

            $parish = $user->parish;
            $parishes = collect([$parish]);
        }

        return view('config.invoice', compact('parish', 'parishes'));
    }

    /**
     * Mettre à jour la configuration des factures
     */
    public function updateInvoiceConfig(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Validation des données
        $validated = $request->validate([
            'parish_id' => 'required|exists:parishes,id',
            // Informations émetteur
            'invoice_company_name' => 'required|string|max:255',
            'invoice_company_description' => 'required|string|max:500',
            'invoice_company_address' => 'nullable|string|max:1000',
            'invoice_company_phone' => 'nullable|string|max:50',
            'invoice_company_email' => 'required|email|max:255',
            'invoice_company_ifu' => 'nullable|string|max:50',
            // Informations client (paroisse)
            'invoice_parish_address' => 'nullable|string|max:1000',
            'invoice_parish_phone' => 'nullable|string|max:50',
            'invoice_parish_contact_name' => 'nullable|string|max:255',
            'invoice_parish_contact_phone' => 'nullable|string|max:50',
            // Paramètres
            'invoice_payment_method' => 'required|string|max:100',
            'invoice_legal_mentions' => 'required|string|max:2000',
        ]);

        $parish = Parish::findOrFail($validated['parish_id']);

        // Vérifier les permissions
        if ($user->role !== 'admin' && $user->parish_id !== $parish->id) {
            abort(403, 'Accès non autorisé à cette paroisse.');
        }

        // Mettre à jour la configuration
        $parish->update([
            'invoice_company_name' => $validated['invoice_company_name'],
            'invoice_company_description' => $validated['invoice_company_description'],
            'invoice_company_address' => $validated['invoice_company_address'],
            'invoice_company_phone' => $validated['invoice_company_phone'],
            'invoice_company_email' => $validated['invoice_company_email'],
            'invoice_company_ifu' => $validated['invoice_company_ifu'],
            'invoice_parish_address' => $validated['invoice_parish_address'],
            'invoice_parish_phone' => $validated['invoice_parish_phone'],
            'invoice_parish_contact_name' => $validated['invoice_parish_contact_name'],
            'invoice_parish_contact_phone' => $validated['invoice_parish_contact_phone'],
            'invoice_payment_method' => $validated['invoice_payment_method'],
            'invoice_legal_mentions' => $validated['invoice_legal_mentions'],
        ]);

        return back()->with('success', 'Configuration des factures mise à jour avec succès !');
    }
}
