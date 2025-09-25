<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Parish;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Afficher la liste des catégories d'une paroisse
     */
    public function index(Parish $parish): JsonResponse
    {
        $categories = $parish->categories;
        return response()->json(['data' => $categories]);
    }

    /**
     * Enregistrer une nouvelle catégorie
     */
    public function store(Request $request, Parish $parish): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'template_file' => ['required', 'file', 'mimes:docx'],
        ]);

        $path = $request->file('template_file')->store('templates/'.$parish->id);

        $absolute = storage_path('app/'.$path);
        $variables = \App\Services\TemplateVariableExtractor::extractVariables($absolute);
        $fields = array_map(function ($name) {
            return ['name' => $name, 'label' => ucfirst(str_replace('_',' ',$name)), 'type' => 'text'];
        }, $variables);

        $category = $parish->categories()->create([
            'name' => $validated['name'],
            'template_file' => $path,
            'fields_json' => $fields,
        ]);

        return response()->json(['data' => $category, 'message' => 'Catégorie créée avec succès'], 201);
    }

    /**
     * Afficher une catégorie spécifique
     */
    public function show(Category $category): JsonResponse
    {
        return response()->json(['data' => $category]);
    }

    /**
     * Mettre à jour une catégorie
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'template_file' => ['nullable', 'file', 'mimes:docx'],
            'fields_json' => ['nullable', 'array'],
        ]);

        $data = [];
        if (array_key_exists('name', $validated)) {
            $data['name'] = $validated['name'];
        }
        if ($request->hasFile('template_file')) {
            $data['template_file'] = $request->file('template_file')->store('templates/'.$category->parish_id);
            $absolute = storage_path('app/'.$data['template_file']);
            $variables = \App\Services\TemplateVariableExtractor::extractVariables($absolute);
            $data['fields_json'] = array_map(function ($name) {
                return ['name' => $name, 'label' => ucfirst(str_replace('_',' ',$name)), 'type' => 'text'];
            }, $variables);
        }

        $category->update($data);
        return response()->json(['data' => $category, 'message' => 'Catégorie mise à jour avec succès']);
    }

    /**
     * Supprimer une catégorie
     */
    public function destroy(Category $category): JsonResponse
    {
        $category->delete();
        return response()->json(['message' => 'Catégorie supprimée avec succès']);
    }

    /**
     * Récupérer les champs d'une catégorie
     */
    public function fields(Category $category): JsonResponse
    {
        return response()->json(['data' => $category->fields_json ?? []]);
    }
}