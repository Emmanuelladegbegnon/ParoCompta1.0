<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Parish;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ParishController extends Controller
{
    /**
     * Afficher la liste des paroisses
     */
    public function index(): JsonResponse
    {
        $parishes = Parish::all();
        return response()->json(['data' => $parishes]);
    }

    /**
     * Enregistrer une nouvelle paroisse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'enable_payment_tracking' => 'boolean',
            'weekly_payment_amount' => 'nullable|numeric',
            'storage_path' => 'nullable|string',
        ]);

        $parish = Parish::create($validated);
        return response()->json(['data' => $parish, 'message' => 'Paroisse créée avec succès'], 201);
    }

    /**
     * Afficher une paroisse spécifique
     */
    public function show(Parish $parish): JsonResponse
    {
        return response()->json(['data' => $parish]);
    }

    /**
     * Mettre à jour une paroisse
     */
    public function update(Request $request, Parish $parish): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'enable_payment_tracking' => 'sometimes|boolean',
            'weekly_payment_amount' => 'nullable|numeric',
            'storage_path' => 'nullable|string',
        ]);

        $parish->update($validated);
        return response()->json(['data' => $parish, 'message' => 'Paroisse mise à jour avec succès']);
    }

    /**
     * Supprimer une paroisse
     */
    public function destroy(Parish $parish): JsonResponse
    {
        $parish->delete();
        return response()->json(['message' => 'Paroisse supprimée avec succès']);
    }
}