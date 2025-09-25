<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-bold text-danger">
            <i class="fas fa-tools me-2"></i>
            Outils Admin - Test de stockage
        </h2>
    </x-slot>

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="parocompta-card p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-folder-open me-2 text-primary"></i>
                        Testeur de chemins de stockage
                    </h5>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Cet outil permet de tester la validité des chemins de stockage</strong><br>
                        Utilisez-le pour vérifier que les dossiers sont accessibles et que ParoCompta peut y créer des fichiers.
                    </div>
                    
                    <!-- Formulaire de test -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-keyboard me-2"></i>
                            Chemin à tester
                        </label>
                        <div class="input-group">
                            <input type="text" id="testPath" class="form-control" 
                                   placeholder="Ex: C:\ParoCompta\Documents" 
                                   value="C:\ParoCompta\Documents">
                            <button type="button" class="btn btn-primary" onclick="testStoragePath()">
                                <i class="fas fa-play me-2"></i>
                                Tester
                            </button>
                        </div>
                    </div>
                    
                    <!-- Chemins suggérés -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-muted">
                            <i class="fas fa-lightbulb me-2"></i>
                            Chemins suggérés à tester :
                        </h6>
                        <div class="btn-group-vertical d-block" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1" onclick="setTestPath('C:\\ParoCompta\\Documents')">
                                C:\ParoCompta\Documents
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1" onclick="setTestPath('C:\\Users\\{{ env('USERNAME', 'Admin') }}\\Documents\\ParoCompta')">
                                C:\Users\{{ env('USERNAME', 'Admin') }}\Documents\ParoCompta
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1" onclick="setTestPath('D:\\Stockage\\ParoCompta')">
                                D:\Stockage\ParoCompta
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1" onclick="setTestPath('public')">
                                public (Stockage Laravel)
                            </button>
                        </div>
                    </div>
                    
                    <!-- Zone de résultats -->
                    <div id="testResults" class="d-none">
                        <h6 class="fw-bold text-muted">
                            <i class="fas fa-clipboard-list me-2"></i>
                            Résultats du test :
                        </h6>
                        <div id="resultsContent"></div>
                    </div>
                    
                    <!-- Zone de chargement -->
                    <div id="loadingTest" class="d-none text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Test en cours...</span>
                        </div>
                        <p class="mt-2 text-muted">Test du chemin en cours...</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Guide d'utilisation -->
        <div class="row justify-content-center mt-4">
            <div class="col-md-10">
                <div class="parocompta-card p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-question-circle me-2 text-info"></i>
                        Guide d'utilisation
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-success">
                                <i class="fas fa-check-circle me-2"></i>
                                Chemin valide
                            </h6>
                            <ul class="text-muted">
                                <li>Le dossier existe</li>
                                <li>Accessible en lecture</li>
                                <li>Accessible en écriture</li>
                                <li>Test de création de fichier réussi</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-danger">
                                <i class="fas fa-times-circle me-2"></i>
                                Problèmes courants
                            </h6>
                            <ul class="text-muted">
                                <li>Dossier inexistant</li>
                                <li>Permissions insuffisantes</li>
                                <li>Chemin mal formaté</li>
                                <li>Disque plein ou protégé</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Important :</strong> Assurez-vous que le service web (Apache/Nginx) a les permissions nécessaires pour accéder au dossier choisi.
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function setTestPath(path) {
            document.getElementById('testPath').value = path;
        }
        
        function testStoragePath() {
            const path = document.getElementById('testPath').value.trim();
            
            if (!path) {
                alert('Veuillez saisir un chemin à tester.');
                return;
            }
            
            // Afficher le chargement
            document.getElementById('loadingTest').classList.remove('d-none');
            document.getElementById('testResults').classList.add('d-none');
            
            // Faire la requête AJAX
            fetch('{{ route("admin.test-path") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ path: path })
            })
            .then(response => response.json())
            .then(data => {
                displayResults(data);
            })
            .catch(error => {
                console.error('Erreur:', error);
                displayError('Erreur lors du test du chemin');
            })
            .finally(() => {
                document.getElementById('loadingTest').classList.add('d-none');
            });
        }
        
        function displayResults(data) {
            const resultsDiv = document.getElementById('resultsContent');
            let html = '';
            
            if (data.error) {
                html = `<div class="alert alert-danger"><i class="fas fa-times me-2"></i>${data.error}</div>`;
            } else {
                // Statut général
                const isValid = data.exists && data.writable && (data.write_test !== false);
                html += `<div class="alert ${isValid ? 'alert-success' : 'alert-danger'}">`;
                html += `<i class="fas fa-${isValid ? 'check' : 'times'} me-2"></i>`;
                html += `<strong>Statut : ${isValid ? 'VALIDE' : 'PROBLÈME DÉTECTÉ'}</strong>`;
                html += `</div>`;
                
                // Détails
                html += '<div class="table-responsive"><table class="table table-sm">';
                html += '<tr><td><strong>Chemin testé</strong></td><td><code>' + data.path + '</code></td></tr>';
                html += '<tr><td><strong>Existe</strong></td><td>' + getStatusBadge(data.exists) + '</td></tr>';
                html += '<tr><td><strong>Lecture</strong></td><td>' + getStatusBadge(data.readable) + '</td></tr>';
                html += '<tr><td><strong>Écriture</strong></td><td>' + getStatusBadge(data.writable) + '</td></tr>';
                
                if (data.exists) {
                    html += '<tr><td><strong>Permissions</strong></td><td><code>' + (data.permissions || 'N/A') + '</code></td></tr>';
                    html += '<tr><td><strong>Propriétaire</strong></td><td>' + (data.owner || 'N/A') + '</td></tr>';
                    html += '<tr><td><strong>Taille</strong></td><td>' + (data.size || 'N/A') + '</td></tr>';
                    html += '<tr><td><strong>Fichiers</strong></td><td>' + (data.files_count || 0) + '</td></tr>';
                    html += '<tr><td><strong>Test écriture</strong></td><td>' + getStatusBadge(data.write_test) + '</td></tr>';
                    
                    if (data.write_error) {
                        html += '<tr><td><strong>Erreur écriture</strong></td><td><span class="text-danger">' + data.write_error + '</span></td></tr>';
                    }
                } else {
                    html += '<tr><td><strong>Peut créer</strong></td><td>' + getStatusBadge(data.can_create) + '</td></tr>';
                    if (data.created) {
                        html += '<tr><td colspan="2"><div class="alert alert-info mb-0"><i class="fas fa-info me-2"></i>Dossier créé automatiquement</div></td></tr>';
                    }
                    if (data.create_error) {
                        html += '<tr><td><strong>Erreur création</strong></td><td><span class="text-danger">' + data.create_error + '</span></td></tr>';
                    }
                }
                
                html += '</table></div>';
            }
            
            resultsDiv.innerHTML = html;
            document.getElementById('testResults').classList.remove('d-none');
        }
        
        function getStatusBadge(status) {
            if (status === true) {
                return '<span class="badge bg-success"><i class="fas fa-check me-1"></i>OUI</span>';
            } else if (status === false) {
                return '<span class="badge bg-danger"><i class="fas fa-times me-1"></i>NON</span>';
            } else {
                return '<span class="badge bg-secondary">N/A</span>';
            }
        }
        
        function displayError(message) {
            const resultsDiv = document.getElementById('resultsContent');
            resultsDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>${message}</div>`;
            document.getElementById('testResults').classList.remove('d-none');
        }
    </script>
    @endpush
</x-app-layout>
