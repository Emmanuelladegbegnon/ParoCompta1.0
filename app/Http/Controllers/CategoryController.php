<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Parish;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Parish $parish): View
    {
        $user = request()->user();
        abort_unless($user->role === 'admin' || $user->parish_id === $parish->id, 403);
        $categories = $parish->categories()->latest()->paginate(10);
        return view('categories.index', compact('parish', 'categories'));
    }

    public function create(Parish $parish): View
    {
        $user = request()->user();
        abort_unless($user->role === 'admin' || $user->parish_id === $parish->id, 403);
        return view('categories.create', compact('parish'));
    }

    public function store(Request $request, Parish $parish): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->role === 'admin' || $user->parish_id === $parish->id, 403);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'template_file' => ['nullable', 'file', 'mimes:docx'],
            'preset' => ['nullable', 'string', 'in:autres_recettes,quetes_paroisse,quetes_stations,autres_quetes'],
        ]);

        $fields = [];
        $path = null;
        if ($request->hasFile('template_file')) {
            $path = $request->file('template_file')->store('templates/'.$parish->id);
            $absolute = storage_path('app/'.$path);
            $variables = \App\Services\TemplateVariableExtractor::extractVariables($absolute);
            $fields = array_map(function ($name) {
                return ['name' => $name, 'label' => ucfirst(str_replace('_',' ',$name)), 'type' => 'text'];
            }, $variables);
        } elseif (($validated['preset'] ?? '') === 'autres_recettes') {
            $fields = \App\Support\CategoryPresets::autresRecettes();
        } elseif (($validated['preset'] ?? '') === 'quetes_paroisse') {
            $fields = \App\Support\CategoryPresets::quetesParoisse();
        } elseif (($validated['preset'] ?? '') === 'quetes_stations') {
            $fields = \App\Support\CategoryPresets::quetesStations();
        } elseif (($validated['preset'] ?? '') === 'autres_quetes') {
            $fields = \App\Support\CategoryPresets::autresQuetes();
        }

        $parish->categories()->create([
            'name' => $validated['name'],
            'template_file' => $path,
            'fields_json' => $fields,
        ]);

        return redirect()->route('parishes.categories.index', $parish)->with('status', 'Catégorie créée. Les champs ont été détectés automatiquement.');
    }

    public function show(Parish $parish, Category $category): View
    {
        $user = request()->user();
        abort_unless($user->role === 'admin' || $user->parish_id === $parish->id, 403);
        return view('categories.show', compact('parish', 'category'));
    }

    public function edit(Parish $parish, Category $category): View
    {
        $user = request()->user();
        abort_unless($user->role === 'admin' || $user->parish_id === $parish->id, 403);
        return view('categories.edit', compact('parish', 'category'));
    }

    public function update(Request $request, Parish $parish, Category $category): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->role === 'admin' || $user->parish_id === $parish->id, 403);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'template_file' => ['nullable', 'file', 'mimes:docx'],
        ]);

        $data = ['name' => $validated['name']];
        if ($request->hasFile('template_file')) {
            $path = $request->file('template_file')->store('templates/'.$parish->id);
            $data['template_file'] = $path;

            $absolute = storage_path('app/'.$path);
            $variables = \App\Services\TemplateVariableExtractor::extractVariables($absolute);
            $fields = array_map(function ($name) {
                return ['name' => $name, 'label' => ucfirst(str_replace('_',' ',$name)), 'type' => 'text'];
            }, $variables);
            $data['fields_json'] = $fields;
        }

        $category->update($data);
        return redirect()->route('parishes.categories.index', $parish)->with('status', 'Catégorie mise à jour. Les champs ont été recalculés selon le modèle.');
    }

    public function destroy(Parish $parish, Category $category): RedirectResponse
    {
        abort_unless(request()->user()->role === 'admin', 403);
        $category->delete();
        return redirect()->route('parishes.categories.index', $parish)->with('status', 'Catégorie supprimée.');
    }
}
