<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminToolsController extends Controller
{
    public function storageTest()
    {
        // Vérifier que l'utilisateur est admin
        abort_unless(request()->user()->role === 'admin', 403);

        return view('admin.storage-test');
    }

    public function testPath(Request $request)
    {
        // Vérifier que l'utilisateur est admin
        abort_unless(request()->user()->role === 'admin', 403);

        $path = $request->input('path');
        $results = [];

        if (empty($path)) {
            return response()->json(['error' => 'Chemin vide']);
        }

        // Tests de base
        $results['path'] = $path;
        $results['exists'] = is_dir($path);
        $results['readable'] = is_readable($path);
        $results['writable'] = is_writable($path);

        if ($results['exists']) {
            // Informations détaillées
            $results['permissions'] = substr(sprintf('%o', fileperms($path)), -4);
            $results['owner'] = function_exists('posix_getpwuid') ? posix_getpwuid(fileowner($path))['name'] ?? 'N/A' : 'N/A';
            $results['size'] = $this->getDirSize($path);
            $results['files_count'] = count(glob($path . DIRECTORY_SEPARATOR . '*'));

            // Test de création de fichier
            $testFile = $path . DIRECTORY_SEPARATOR . 'test_parocompta_' . time() . '.tmp';
            try {
                if (file_put_contents($testFile, 'Test ParoCompta - ' . date('Y-m-d H:i:s')) !== false) {
                    $results['write_test'] = true;
                    unlink($testFile);
                } else {
                    $results['write_test'] = false;
                }
            } catch (\Exception $e) {
                $results['write_test'] = false;
                $results['write_error'] = $e->getMessage();
            }
        } else {
            // Tenter de créer le dossier
            $results['can_create'] = false;
            try {
                if (mkdir($path, 0755, true)) {
                    $results['can_create'] = true;
                    $results['created'] = true;
                    $results['exists'] = true;
                    $results['writable'] = is_writable($path);
                }
            } catch (\Exception $e) {
                $results['create_error'] = $e->getMessage();
            }
        }

        return response()->json($results);
    }

    private function getDirSize($path)
    {
        $size = 0;
        try {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($files as $file) {
                $size += $file->getSize();
            }
        } catch (\Exception $e) {
            return 'N/A';
        }

        return $this->formatBytes($size);
    }

    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, $precision) . ' ' . $units[$i];
    }
}
