@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">📁 Fichiers de {{ $parish->name }}</h1>
                    <p class="text-muted mb-0">Structure des documents générés par trimestre</p>
                </div>
                <div>
                    <a href="{{ route('parishes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Retour aux paroisses
                    </a>
                    <a href="{{ route('parishes.edit', $parish) }}" class="btn btn-primary">
                        <i class="fas fa-cog"></i> Configurer
                    </a>
                </div>
            </div>

            <!-- Informations de stockage -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-folder-open text-primary"></i> Configuration de stockage
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Chemin de stockage :</strong><br>
                            <code class="text-primary">{{ $parish->storage_path ?: 'public (Laravel par défaut)' }}</code>
                        </div>
                        <div class="col-md-6">
                            <strong>Chemin complet :</strong><br>
                            <code class="text-muted">{{ $parish->getStorageBasePath() }}</code>
                        </div>
                    </div>
                </div>
            </div>

            @if(empty($structure))
                <!-- Aucun fichier -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Aucun fichier généré</h4>
                        <p class="text-muted mb-4">
                            Les fichiers Word apparaîtront ici une fois que vous aurez créé vos premières fiches comptables.
                        </p>
                        <a href="{{ route('entries.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Créer une fiche
                        </a>
                    </div>
                </div>
            @else
                <!-- Structure des fichiers -->
                <div class="row">
                    @foreach($structure as $quarter => $categories)
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-calendar-alt"></i> {{ ucfirst(str_replace('_', ' ', $quarter)) }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if(empty($categories))
                                        <p class="text-muted mb-0">
                                            <i class="fas fa-info-circle"></i> Aucune catégorie dans ce trimestre
                                        </p>
                                    @else
                                        @foreach($categories as $category => $files)
                                            <div class="mb-3">
                                                <h6 class="text-primary mb-2">
                                                    <i class="fas fa-folder"></i> {{ $category }}
                                                    <span class="badge bg-secondary ms-2">{{ count($files) }} fichier(s)</span>
                                                </h6>
                                                
                                                @if(empty($files))
                                                    <p class="text-muted small mb-0 ms-3">Aucun fichier</p>
                                                @else
                                                    <div class="ms-3">
                                                        @foreach($files as $file)
                                                            @php
                                                                $filePath = $parish->getStorageBasePath() . DIRECTORY_SEPARATOR . $quarter . DIRECTORY_SEPARATOR . $category . DIRECTORY_SEPARATOR . $file;
                                                                $fileInfo = \App\Services\FileOrganizer::getFileInfo($filePath);
                                                            @endphp
                                                            <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                                                <div>
                                                                    <i class="fas fa-file-word text-primary"></i>
                                                                    <span class="ms-2">{{ $file }}</span>
                                                                </div>
                                                                <div class="text-end">
                                                                    @if(!empty($fileInfo))
                                                                        <small class="text-muted d-block">{{ $fileInfo['size_formatted'] }}</small>
                                                                        <small class="text-muted">{{ date('d/m/Y H:i', strtotime($fileInfo['modified_at'])) }}</small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Statistiques -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-chart-bar text-success"></i> Statistiques
                        </h5>
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="border-end">
                                    <h3 class="text-primary mb-1">{{ count($structure) }}</h3>
                                    <small class="text-muted">Trimestres</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border-end">
                                    @php
                                        $totalCategories = 0;
                                        foreach($structure as $categories) {
                                            $totalCategories += count($categories);
                                        }
                                    @endphp
                                    <h3 class="text-success mb-1">{{ $totalCategories }}</h3>
                                    <small class="text-muted">Catégories</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border-end">
                                    @php
                                        $totalFiles = 0;
                                        foreach($structure as $categories) {
                                            foreach($categories as $files) {
                                                $totalFiles += count($files);
                                            }
                                        }
                                    @endphp
                                    <h3 class="text-warning mb-1">{{ $totalFiles }}</h3>
                                    <small class="text-muted">Fichiers</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                @php
                                    $totalSize = 0;
                                    foreach($structure as $quarter => $categories) {
                                        foreach($categories as $category => $files) {
                                            foreach($files as $file) {
                                                $filePath = $parish->getStorageBasePath() . DIRECTORY_SEPARATOR . $quarter . DIRECTORY_SEPARATOR . $category . DIRECTORY_SEPARATOR . $file;
                                                if(file_exists($filePath)) {
                                                    $totalSize += filesize($filePath);
                                                }
                                            }
                                        }
                                    }
                                    $totalSizeFormatted = $totalSize > 0 ? \App\Services\FileOrganizer::formatBytes($totalSize) : '0 B';
                                @endphp
                                <h3 class="text-info mb-1">{{ $totalSizeFormatted }}</h3>
                                <small class="text-muted">Taille totale</small>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
