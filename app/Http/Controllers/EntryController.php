<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Entry;
use App\Models\Parish;
use App\Services\FileOrganizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Services\WordDocumentService;
use Carbon\Carbon;

class EntryController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Entry::with(['category', 'parish'])->latest();
        if ($user->role === 'user' && $user->parish_id) {
            $query->where('parish_id', $user->parish_id);
        }
        $month = (int)$request->input('month', 0);
        $quarter = (int)$request->input('quarter', 0);
        $year = (int)$request->input('year', 0);
        if ($year > 0) {
            $query->whereYear('start_date', $year);
        }
        if ($month > 0) {
            $query->whereMonth('start_date', $month);
        } elseif ($quarter > 0) {
            $months = range(($quarter - 1) * 3 + 1, ($quarter - 1) * 3 + 3);
            $query->whereIn(\Illuminate\Support\Facades\DB::raw('MONTH(start_date)'), $months);
        }
        $entries = $query->paginate(15)->appends($request->only('month','quarter','year'));
        return view('entries.index', compact('entries', 'month', 'quarter', 'year'));
    }


    public function create(Request $request): View
    {
        $user = $request->user();
        $parishes = Parish::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        if ($user->role === 'user' && $user->parish_id) {
            $parishes = Parish::where('id', $user->parish_id)->get();
            $categories = Category::where('parish_id', $user->parish_id)->orderBy('name')->get();
        }
        return view('entries.create', compact('parishes', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'parish_id' => ['required', 'exists:parishes,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'week_label' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'data' => ['required_without:document', 'array'],
            'document' => ['nullable', 'file', 'mimes:doc,docx', 'max:10240'],
        ]);
        if ($user->role !== 'admin' && $user->parish_id != $validated['parish_id']) {
            abort(403);
        }
        $parish = Parish::findOrFail($validated['parish_id']);
        $category = Category::findOrFail($validated['category_id']);
        if ($user->role !== 'admin' && $category->parish_id != $user->parish_id) {
            abort(403);
        }

        $entry = Entry::create([
            'user_id' => $user->id,
            'parish_id' => $parish->id,
            'category_id' => $category->id,
            'week_label' => $validated['week_label'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'data_json' => $validated['data'] ?? [],
        ]);

        // Utiliser la nouvelle logique d'organisation des fichiers
        $directoryPath = $entry->getDirectoryPath();
        $filePath = $entry->getFilePath();
        $fileName = $entry->getFileName();

        // Créer les dossiers nécessaires automatiquement
        if (!$entry->ensureDirectoryExists()) {
            return back()->withErrors(['error' => 'Impossible de créer le dossier de stockage.'])->withInput();
        }

        // Si un fichier a été téléchargé, l'utiliser directement
        if ($request->hasFile('document')) {
            $uploadedFile = $request->file('document');
            $filePath = $entry->getFilePath();
            $directoryPath = $entry->getDirectoryPath();
            $fileName = $entry->getFileName();

            // Déplacer le fichier vers le bon emplacement
            $uploadedFile->move($directoryPath, $fileName);

            $entry->update(['generated_file' => $filePath]);
            return redirect()->route('entries.index')->with('status', 'Fiche téléchargée avec succès.');
        }
        // Sinon, générer le document via template s'il existe, sinon via preset
        else {
            $filePath = $entry->getFilePath();

            if (!empty($category->template_file)) {
                $templatePath = storage_path('app/' . $category->template_file);
                $processor = new TemplateProcessor($templatePath);
                foreach (($validated['data'] ?? []) as $key => $value) {
                    $processor->setValue($key, (string)$value);
                }

                $tempFile = tempnam(sys_get_temp_dir(), 'pc_') . '.docx';
                $processor->saveAs($tempFile);

                // Copier le fichier vers le bon emplacement
                copy($tempFile, $filePath);
                unlink($tempFile); // Supprimer le fichier temporaire

            } else {
                $service = app(WordDocumentService::class);
                $filePath = $service->generateFromPreset($entry, $category);
            }

            $entry->update(['generated_file' => $filePath]);
            return redirect()->route('entries.index')->with('status', 'Fiche générée automatiquement.');
        }
    }

    public function show(Entry $entry): View
    {
        return view('entries.show', compact('entry'));
    }

    public function edit(Entry $entry): View
    {
        $user = request()->user();
        abort_unless($user->role === 'admin' || $user->parish_id === $entry->parish_id, 403);
        $parishes = Parish::orderBy('name')->get();
        $categories = Category::where('parish_id', $entry->parish_id)->orderBy('name')->get();
        return view('entries.edit', compact('entry', 'parishes', 'categories'));
    }

    public function update(Request $request, Entry $entry): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'parish_id' => ['required', 'exists:parishes,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'week_label' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'data' => ['required', 'array'],
        ]);
        if ($user->role !== 'admin' && $user->parish_id != $validated['parish_id']) {
            abort(403);
        }

        $entry->update([
            'parish_id' => $validated['parish_id'],
            'category_id' => $validated['category_id'],
            'week_label' => $validated['week_label'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'data_json' => $validated['data'],
        ]);

        return redirect()->route('entries.index')->with('status', 'Fiche mise à jour.');
    }

    public function destroy(Entry $entry): RedirectResponse
    {
        $user = request()->user();
        abort_unless($user->role === 'admin' || $user->parish_id === $entry->parish_id, 403);

        // Supprimer le fichier généré s'il existe
        if ($entry->generated_file) {
            if ($entry->parish && $entry->parish->isAbsoluteStorageBase()) {
                // Stockage absolu (disque local)
                if (is_file($entry->generated_file)) {
                    unlink($entry->generated_file);
                }
            } else {
                // Stockage Laravel (storage/app)
                if (Storage::exists($entry->generated_file)) {
                    Storage::delete($entry->generated_file);
                }
            }
        }

        $entry->delete();
        return redirect()->route('entries.index')->with('success', 'Fiche supprimée avec succès.');
    }

    public function download(Entry $entry)
    {
        abort_unless($entry->generated_file, 404);
        $path = $entry->generated_file;
        if ($entry->parish && $entry->parish->isAbsoluteStorageBase()) {
            abort_unless(is_file($path), 404);
            return response()->download($path);
        }
        abort_unless(Storage::exists($path), 404);
        return Storage::download($path);
    }

    public function fields(Category $category)
    {
        return response()->json($category->fields_json ?? []);
    }
}
