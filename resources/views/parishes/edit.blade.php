<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-bold text-primary">
            <i class="fas fa-edit me-2"></i>
            Modifier la paroisse : {{ $parish->name }}
        </h2>
    </x-slot>

    <div class="container-fluid py-4">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Erreurs de validation :</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="parocompta-card p-4">
                    <h5 class="fw-bold mb-4">
                        <i class="fas fa-church me-2 text-primary"></i>
                        Paramètres de la paroisse
                    </h5>

                    <form method="POST" action="{{ route('parishes.update', $parish) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-tag me-2"></i>
                                Nom de la paroisse
                            </label>
                            <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $parish->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="enable_payment_tracking"
                                       value="1" id="enable_payment_tracking"
                                       {{ old('enable_payment_tracking', $parish->enable_payment_tracking) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="enable_payment_tracking">
                                    <i class="fas fa-euro-sign me-2"></i>
                                    Activer le suivi des paiements
                                </label>
                            </div>
                            <small class="text-muted">
                                Permet aux saisissants de suivre leurs rémunérations hebdomadaires
                            </small>
                        </div>

                        <div class="mb-3" id="payment_amount_field">
                            <label class="form-label fw-bold">
                                <i class="fas fa-coins me-2"></i>
                                Montant hebdomadaire (FCFA)
                            </label>
                            <input name="weekly_payment_amount" type="number" step="1" min="0"
                                   class="form-control @error('weekly_payment_amount') is-invalid @enderror"
                                   value="{{ old('weekly_payment_amount', $parish->weekly_payment_amount) }}">
                            @error('weekly_payment_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Montant payé par semaine de saisie (ex: 1000 FCFA par semaine)
                            </small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-folder me-2"></i>
                                Dossier de stockage
                            </label>

                            <!-- Options prédéfinies -->
                            <div class="mb-2">
                                <small class="text-muted fw-bold">Chemins suggérés :</small>
                                <div class="btn-group-vertical d-block mt-1" role="group">
                                    <button type="button" class="btn btn-outline-info btn-sm mb-1" onclick="setStoragePath('C:\\ParoCompta\\Documents')">
                                        <i class="fas fa-folder me-2"></i>C:\ParoCompta\Documents
                                    </button>
                                    <button type="button" class="btn btn-outline-info btn-sm mb-1" onclick="setStoragePath('C:\\Users\\{{ env('USERNAME', 'VotreNom') }}\\Documents\\ParoCompta')">
                                        <i class="fas fa-user-folder me-2"></i>C:\Users\{{ env('USERNAME', 'VotreNom') }}\Documents\ParoCompta
                                    </button>
                                    <button type="button" class="btn btn-outline-info btn-sm mb-1" onclick="setStoragePath('D:\\Stockage\\ParoCompta')">
                                        <i class="fas fa-hdd me-2"></i>D:\Stockage\ParoCompta
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm mb-1" onclick="setStoragePath('public')">
                                        <i class="fas fa-server me-2"></i>Stockage Laravel (par défaut)
                                    </button>
                                </div>
                            </div>

                            <!-- Champ de saisie -->
                            <div class="input-group">
                                <input name="storage_path" type="text" class="form-control @error('storage_path') is-invalid @enderror"
                                       value="{{ old('storage_path', $parish->storage_path) }}" id="storage_path"
                                       placeholder="Saisissez le chemin complet ou utilisez les suggestions ci-dessus">
                                <button type="button" class="btn btn-outline-primary" onclick="showPathHelper()">
                                    <i class="fas fa-question-circle me-2"></i>
                                    Aide
                                </button>
                                <button type="button" class="btn btn-outline-success" onclick="testStoragePath()">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Tester
                                </button>
                            </div>

                            @error('storage_path')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <!-- Zone de feedback -->
                            <div id="path-feedback" class="mt-2"></div>

                            <small class="text-muted">
                                <strong>Exemples valides :</strong><br>
                                • <code>C:\ParoCompta\Documents</code> (Windows)<br>
                                • <code>D:\Stockage\ParoCompta</code> (Disque D:)<br>
                                • <code>public</code> (Stockage Laravel par défaut)
                            </small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('parishes.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Retour
                            </a>
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="fas fa-save me-2"></i>
                                Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Afficher/masquer le champ montant selon l'activation du suivi
        document.getElementById('enable_payment_tracking').addEventListener('change', function() {
            const paymentField = document.getElementById('payment_amount_field');
            if (this.checked) {
                paymentField.style.display = 'block';
            } else {
                paymentField.style.display = 'none';
            }
        });

        // Initialiser l'affichage au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const checkbox = document.getElementById('enable_payment_tracking');
            const paymentField = document.getElementById('payment_amount_field');
            paymentField.style.display = checkbox.checked ? 'block' : 'none';
        });

        // Validation en temps réel du chemin de stockage
        let validationTimeout;
        document.getElementById('storage_path').addEventListener('input', function() {
            clearTimeout(validationTimeout);
            const path = this.value.trim();
            const feedbackDiv = document.getElementById('path-feedback');

            if (path === '' || path === 'public') {
                feedbackDiv.className = 'alert alert-success py-2 mt-2';
                feedbackDiv.innerHTML = '<i class="fas fa-check me-2"></i>Stockage Laravel par défaut - Aucune configuration requise';
                return;
            }

            feedbackDiv.className = 'alert alert-info py-2 mt-2';
            feedbackDiv.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Validation en cours...';

            validationTimeout = setTimeout(() => {
                validatePath(path, feedbackDiv);
            }, 500);
        });

        function validatePath(path, feedbackElement) {
            fetch('{{ route("parishes.validate-storage-path") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ path: path })
            })
            .then(response => response.json())
            .then(data => {
                if (data.valid) {
                    feedbackElement.className = 'alert alert-success py-2 mt-2';
                    feedbackElement.innerHTML = '<i class="fas fa-check me-2"></i>' + data.message;
                } else {
                    feedbackElement.className = 'alert alert-danger py-2 mt-2';
                    feedbackElement.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>' + data.message;

                    if (data.can_create) {
                        feedbackElement.innerHTML += '<br><button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="createDirectory(\'' + path + '\')"><i class="fas fa-plus me-1"></i>Créer le dossier</button>';
                    }
                }
            })
            .catch(error => {
                feedbackElement.className = 'alert alert-warning py-2 mt-2';
                feedbackElement.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Erreur de validation du chemin';
            });
        }

        // Fonction pour définir un chemin prédéfini
        function setStoragePath(path) {
            document.getElementById('storage_path').value = path;
            document.getElementById('storage_path').dispatchEvent(new Event('input'));
        }

        // Fonction pour tester le chemin
        function testStoragePath() {
            const path = document.getElementById('storage_path').value.trim();
            if (!path) {
                alert('Veuillez saisir un chemin avant de le tester.');
                return;
            }

            const feedbackDiv = document.getElementById('path-feedback');
            feedbackDiv.className = 'alert alert-info py-2 mt-2';
            feedbackDiv.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Test du chemin en cours...';

            validatePath(path, feedbackDiv);
        }

        // Fonction d'aide pour les chemins
        function showPathHelper() {
            const helpModal = `
                <div class="modal fade" id="pathHelpModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="fas fa-question-circle me-2"></i>Aide pour le chemin de stockage</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <h6><i class="fas fa-info-circle me-2 text-info"></i>Comment trouver le bon chemin ?</h6>
                                <ol>
                                    <li><strong>Ouvrez l'Explorateur Windows</strong> (touche Windows + E)</li>
                                    <li><strong>Naviguez vers le dossier</strong> où vous voulez stocker les fichiers</li>
                                    <li><strong>Cliquez dans la barre d'adresse</strong> en haut de l'explorateur</li>
                                    <li><strong>Copiez le chemin complet</strong> qui s'affiche (ex: C:\\Users\\VotreNom\\Documents)</li>
                                    <li><strong>Collez-le dans le champ</strong> ci-dessus</li>
                                </ol>

                                <h6 class="mt-4"><i class="fas fa-folder me-2 text-warning"></i>Exemples de chemins valides :</h6>
                                <div class="bg-light p-3 rounded">
                                    <code>C:\\ParoCompta\\Documents</code><br>
                                    <code>C:\\Users\\VotreNom\\Documents\\ParoCompta</code><br>
                                    <code>D:\\Stockage\\ParoCompta</code><br>
                                    <code>E:\\Backup\\ParoCompta</code>
                                </div>

                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-lightbulb me-2"></i>
                                    <strong>Conseil :</strong> Créez un dossier dédié comme "ParoCompta" dans vos Documents pour mieux organiser vos fichiers.
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Supprimer le modal existant s'il y en a un
            const existingModal = document.getElementById('pathHelpModal');
            if (existingModal) {
                existingModal.remove();
            }

            // Ajouter le nouveau modal
            document.body.insertAdjacentHTML('beforeend', helpModal);

            // Afficher le modal
            const modal = new bootstrap.Modal(document.getElementById('pathHelpModal'));
            modal.show();
        }

        // Fonction pour créer un dossier (via AJAX)
        function createDirectory(path) {
            if (confirm('Voulez-vous créer le dossier "' + path + '" ?')) {
                fetch('{{ route("parishes.validate-storage-path") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ path: path, create: true })
                })
                .then(response => response.json())
                .then(data => {
                    const feedbackDiv = document.getElementById('path-feedback');
                    if (data.valid) {
                        feedbackDiv.className = 'alert alert-success py-2 mt-2';
                        feedbackDiv.innerHTML = '<i class="fas fa-check me-2"></i>Dossier créé avec succès !';
                    } else {
                        feedbackDiv.className = 'alert alert-danger py-2 mt-2';
                        feedbackDiv.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>' + data.message;
                    }
                });
            }
        }
    </script>
    @endpush
</x-app-layout>



