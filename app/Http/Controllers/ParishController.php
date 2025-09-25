<?php

namespace App\Http\Controllers;

use App\Models\Parish;
use App\Services\FileOrganizer;
use App\Services\DefaultCategoriesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParishController extends Controller
{
    public function index(): View
    {
        $parishes = Parish::latest()->paginate(10);
        return view('parishes.index', compact('parishes'));
    }

    public function create(): View
    {
        return view('parishes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'enable_payment_tracking' => ['nullable', 'boolean'],
            'weekly_payment_amount' => ['nullable', 'numeric', 'min:0'],
            'storage_path' => ['nullable', 'string'],
        ]);

        $validated['enable_payment_tracking'] = (bool)($validated['enable_payment_tracking'] ?? false);
        if (!isset($validated['storage_path']) || $validated['storage_path'] === '') {
            $validated['storage_path'] = 'public';
        }

        $parish = Parish::create($validated);

        // Créer automatiquement les 4 catégories par défaut
        DefaultCategoriesService::createDefaultCategoriesForParish($parish);

        return redirect()->route('parishes.index')->with('status', 'Paroisse créée avec les 4 catégories par défaut.');
    }

    public function show(Parish $parish): View
    {
        return view('parishes.show', compact('parish'));
    }

    public function edit(Parish $parish): View
    {
        return view('parishes.edit', compact('parish'));
    }

    public function update(Request $request, Parish $parish): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'enable_payment_tracking' => ['nullable', 'boolean'],
            'weekly_payment_amount' => ['nullable', 'numeric', 'min:0'],
            'storage_path' => ['nullable', 'string'],
        ]);

        $validated['enable_payment_tracking'] = (bool)($validated['enable_payment_tracking'] ?? false);
        if (!isset($validated['storage_path']) || $validated['storage_path'] === '') {
            $validated['storage_path'] = 'public';
        }

        // Valider le chemin de stockage s'il s'agit d'un chemin absolu
        $storagePath = $validated['storage_path'];

        if ($storagePath !== 'public' &&
            (str_starts_with($storagePath, '/') || preg_match('/^[A-Za-z]:/', $storagePath))) {

            // Essayer de créer le dossier s'il n'existe pas
            if (!is_dir($storagePath)) {
                if (!mkdir($storagePath, 0755, true)) {
                    return back()->withErrors([
                        'storage_path' => 'Impossible de créer le dossier de stockage. Vérifiez les permissions.'
                    ])->withInput();
                }
            }

            // Vérifier que le dossier est accessible en écriture
            if (!is_writable($storagePath)) {
                return back()->withErrors([
                    'storage_path' => 'Le dossier de stockage n\'est pas accessible en écriture.'
                ])->withInput();
            }
        }

        $parish->update($validated);

        return redirect()->route('parishes.index')->with('success', 'Paroisse mise à jour avec succès.');
    }

    public function destroy(Parish $parish): RedirectResponse
    {
        abort_unless(request()->user()->role === 'admin', 403);
        $parish->delete();
        return redirect()->route('parishes.index')->with('status', 'Paroisse supprimée.');
    }

    /**
     * Valider un chemin de stockage via AJAX
     */
    public function validateStoragePath(Request $request)
    {
        $path = $request->input('path');
        $create = $request->input('create', false);

        if (empty($path)) {
            return response()->json(['valid' => false, 'message' => 'Chemin vide']);
        }

        // Stockage Laravel par défaut
        if ($path === 'public') {
            return response()->json([
                'valid' => true,
                'message' => 'Utilisation du stockage Laravel par défaut - Aucune configuration requise.'
            ]);
        }

        // Vérifier si c'est un chemin absolu
        if (str_starts_with($path, '/') || preg_match('/^[A-Za-z]:/', $path)) {

            // Si demande de création
            if ($create && !is_dir($path)) {
                if (mkdir($path, 0755, true)) {
                    return response()->json([
                        'valid' => true,
                        'message' => 'Dossier créé avec succès et accessible en écriture.'
                    ]);
                } else {
                    return response()->json([
                        'valid' => false,
                        'message' => 'Impossible de créer le dossier. Vérifiez les permissions du dossier parent.'
                    ]);
                }
            }

            // Vérifier si le dossier existe
            if (!is_dir($path)) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Le dossier n\'existe pas.',
                    'can_create' => true
                ]);
            }

            // Vérifier les permissions d'écriture
            if (!is_writable($path)) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Le dossier existe mais n\'est pas accessible en écriture. Vérifiez les permissions.'
                ]);
            }

            // Tester la création d'un fichier temporaire
            $testFile = $path . DIRECTORY_SEPARATOR . 'test_parocompta_' . time() . '.tmp';
            if (file_put_contents($testFile, 'test') !== false) {
                unlink($testFile); // Supprimer le fichier de test
                return response()->json([
                    'valid' => true,
                    'message' => 'Dossier valide et accessible en écriture. Test de création de fichier réussi.'
                ]);
            } else {
                return response()->json([
                    'valid' => false,
                    'message' => 'Le dossier existe mais la création de fichiers a échoué.'
                ]);
            }
        }

        return response()->json([
            'valid' => false,
            'message' => 'Format de chemin non reconnu. Utilisez un chemin absolu (ex: C:\\ParoCompta\\Documents).'
        ]);
    }

    /**
     * Affiche la structure des fichiers d'une paroisse
     */
    public function files(Parish $parish): View
    {
        $user = request()->user();
        abort_unless($user->role === 'admin' || $user->parish_id === $parish->id, 403);

        $structure = FileOrganizer::getDirectoryStructure($parish);

        return view('parishes.files', compact('parish', 'structure'));
    }
}
