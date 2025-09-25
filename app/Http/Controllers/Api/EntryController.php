<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entry;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;

class EntryController extends Controller
{
    /**
     * Afficher la liste des entrées
     */
    public function index(Request $request): JsonResponse
    {
        $query = Entry::with(['category', 'parish']);
        
        if ($request->filled('parish_id')) {
            $query->where('parish_id', $request->parish_id);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('year')) {
            $query->whereYear('start_date', (int)$request->year);
        }
        if ($request->filled('month')) {
            $query->whereMonth('start_date', (int)$request->month);
        }
        
        $entries = $query->latest()->paginate(15);
        return response()->json($entries);
    }

    /**
     * Enregistrer une nouvelle entrée
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'parish_id' => ['required', 'exists:parishes,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'week_label' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'data' => ['required_without:document', 'array'],
            'document' => ['nullable', 'file', 'mimes:doc,docx', 'max:10240'],
        ]);

        $entry = Entry::create([
            'parish_id' => $validated['parish_id'],
            'category_id' => $validated['category_id'],
            'week_label' => $validated['week_label'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'data_json' => $validated['data'] ?? [],
        ]);

        $category = Category::findOrFail($validated['category_id']);
        $parish = $entry->parish;

        $start = \Carbon\Carbon::parse($validated['start_date']);
        $monthName = ucfirst($start->locale('fr')->translatedFormat('F'));
        $quarter = 'trimestre_' . (int)ceil($start->quarter);
        $categoryFolder = 'categorie_' . $category->id;

        $base = $parish->getStorageBasePath();
        $dir = $base . '/paroisse_' . $parish->id . '/' . $quarter . '/' . $categoryFolder;
        if ($parish->isAbsoluteStorageBase()) {
            if (!is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }
        } else {
            Storage::makeDirectory($dir);
        }
        $filename = str_replace(' ', '_', $monthName . '_' . $validated['week_label']) . '.docx';
        $relativePath = $dir . '/' . $filename;

        if ($request->hasFile('document')) {
            if ($parish->isAbsoluteStorageBase()) {
                $relativePath = rtrim($dir, '/').'/'.$filename;
                $request->file('document')->move($dir, $filename);
            } else {
                Storage::putFileAs($dir, $request->file('document'), $filename);
            }
            $entry->update(['generated_file' => $relativePath]);
        } else {
            $templatePath = storage_path('app/' . $category->template_file);
            $processor = new TemplateProcessor($templatePath);
            foreach ($validated['data'] as $key => $value) {
                $processor->setValue($key, (string)$value);
            }
            $tempFile = tempnam(sys_get_temp_dir(), 'pc_') . '.docx';
            $processor->saveAs($tempFile);
            if ($parish->isAbsoluteStorageBase()) {
                $relativePath = rtrim($dir, '/').'/'.$filename;
                @copy($tempFile, $relativePath);
            } else {
                Storage::put($relativePath, file_get_contents($tempFile));
            }
            $entry->update(['generated_file' => $relativePath]);
        }

        return response()->json([
            'data' => $entry->fresh(['category','parish']),
            'message' => 'Entrée créée avec succès',
            'file_url' => route('entries.download', $entry),
        ], 201);
    }

    /**
     * Afficher une entrée spécifique
     */
    public function show(Entry $entry): JsonResponse
    {
        $entry->load(['category', 'parish']);
        return response()->json(['data' => $entry]);
    }

    /**
     * Mettre à jour une entrée
     */
    public function update(Request $request, Entry $entry): JsonResponse
    {
        $validated = $request->validate([
            'parish_id' => ['sometimes', 'exists:parishes,id'],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'week_label' => ['sometimes', 'string', 'max:255'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after_or_equal:start_date'],
            'data' => ['sometimes', 'array'],
        ]);

        $entry->update([
            'parish_id' => $validated['parish_id'] ?? $entry->parish_id,
            'category_id' => $validated['category_id'] ?? $entry->category_id,
            'week_label' => $validated['week_label'] ?? $entry->week_label,
            'start_date' => $validated['start_date'] ?? $entry->start_date,
            'end_date' => $validated['end_date'] ?? $entry->end_date,
            'data_json' => $validated['data'] ?? $entry->data_json,
        ]);

        return response()->json([
            'data' => $entry->fresh(['category','parish']),
            'message' => 'Entrée mise à jour avec succès',
        ]);
    }

    /**
     * Supprimer une entrée
     */
    public function destroy(Entry $entry): JsonResponse
    {
        if ($entry->generated_file && Storage::exists($entry->generated_file)) {
            Storage::delete($entry->generated_file);
        }
        $entry->delete();
        return response()->json(['message' => 'Entrée supprimée avec succès']);
    }

    /**
     * Télécharger le document Word généré
     */
    public function download(Entry $entry)
    {
        if (!$entry->generated_file || !Storage::exists($entry->generated_file)) {
            return response()->json(['message' => 'Fichier non trouvé'], 404);
        }
        return Storage::download($entry->generated_file);
    }
}