<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="fw-bold text-primary mb-0">
                <i class="fas fa-file-invoice me-2"></i>
                Configuration des Factures - {{ $parish->name }}
            </h2>
            <div class="text-muted">
                <small>
                    <i class="fas fa-info-circle me-1"></i>
                    Configurez les informations qui apparaîtront sur vos factures
                </small>
            </div>
        </div>
    </x-slot>

    <div class="container-fluid py-4">
        <!-- Messages de succès/erreur -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Erreurs de validation :</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Sélecteur de paroisse pour admin -->
        @if(auth()->user()->role === 'admin' && $parishes->count() > 1)
        <div class="row mb-4">
            <div class="col-12">
                <div class="parocompta-card p-3">
                    <form method="GET" class="row align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Paroisse à configurer</label>
                            <select name="parish_id" class="form-select" onchange="this.form.submit()">
                                @foreach($parishes as $p)
                                <option value="{{ $p->id }}" {{ $parish->id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <!-- Aide et instructions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="fas fa-lightbulb fa-2x text-info"></i>
                        </div>
                        <div>
                            <h5 class="alert-heading mb-2">
                                <i class="fas fa-info-circle me-1"></i>
                                Comment configurer vos factures
                            </h5>
                            <p class="mb-2">
                                Cette page vous permet de personnaliser toutes les informations qui apparaîtront sur vos factures générées automatiquement.
                            </p>
                            <ul class="mb-0">
                                <li><strong>Informations Émetteur :</strong> Vos coordonnées (ParoCompta Services)</li>
                                <li><strong>Informations Client :</strong> Les coordonnées de votre paroisse</li>
                                <li><strong>Paramètres :</strong> Mode de paiement et mentions légales</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulaire de configuration -->
        <form method="POST" action="{{ route('config.invoice.update') }}">
            @csrf
            <input type="hidden" name="parish_id" value="{{ $parish->id }}">

            <!-- Informations de l'émetteur -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="parocompta-card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-building me-2"></i>
                                Informations de l'Émetteur (ParoCompta Services)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nom de l'entreprise *</label>
                                        <input type="text" name="invoice_company_name" class="form-control" 
                                               value="{{ old('invoice_company_name', $parish->invoice_company_name) }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Description des services *</label>
                                        <textarea name="invoice_company_description" class="form-control" rows="2" required>{{ old('invoice_company_description', $parish->invoice_company_description) }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Email de contact *</label>
                                        <input type="email" name="invoice_company_email" class="form-control" 
                                               value="{{ old('invoice_company_email', $parish->invoice_company_email) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Adresse complète</label>
                                        <textarea name="invoice_company_address" class="form-control" rows="3" 
                                                  placeholder="Rue, ville, code postal, pays">{{ old('invoice_company_address', $parish->invoice_company_address) }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Téléphone</label>
                                        <input type="text" name="invoice_company_phone" class="form-control" 
                                               value="{{ old('invoice_company_phone', $parish->invoice_company_phone) }}" 
                                               placeholder="+225 XX XX XX XX">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Numéro IFU (si applicable)</label>
                                        <input type="text" name="invoice_company_ifu" class="form-control" 
                                               value="{{ old('invoice_company_ifu', $parish->invoice_company_ifu) }}" 
                                               placeholder="Numéro d'identification fiscale">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations du client (paroisse) -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="parocompta-card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-church me-2"></i>
                                Informations du Client ({{ $parish->name }})
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nom du contact principal</label>
                                        <input type="text" name="invoice_parish_contact_name" class="form-control" 
                                               value="{{ old('invoice_parish_contact_name', $parish->invoice_parish_contact_name) }}" 
                                               placeholder="Nom du responsable paroissial">
                                        <small class="text-muted">Si vide, le nom de l'utilisateur sera utilisé</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Téléphone du contact</label>
                                        <input type="text" name="invoice_parish_contact_phone" class="form-control" 
                                               value="{{ old('invoice_parish_contact_phone', $parish->invoice_parish_contact_phone) }}" 
                                               placeholder="+225 XX XX XX XX">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Adresse de la paroisse</label>
                                        <textarea name="invoice_parish_address" class="form-control" rows="3" 
                                                  placeholder="Adresse complète de la paroisse">{{ old('invoice_parish_address', $parish->invoice_parish_address) }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Téléphone de la paroisse</label>
                                        <input type="text" name="invoice_parish_phone" class="form-control" 
                                               value="{{ old('invoice_parish_phone', $parish->invoice_parish_phone) }}" 
                                               placeholder="+225 XX XX XX XX">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paramètres de facturation -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="parocompta-card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="fas fa-file-invoice me-2"></i>
                                Paramètres de Facturation
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Mode de paiement par défaut *</label>
                                        <select name="invoice_payment_method" class="form-select" required>
                                            <option value="Espèces" {{ old('invoice_payment_method', $parish->invoice_payment_method) == 'Espèces' ? 'selected' : '' }}>Espèces</option>
                                            <option value="Virement bancaire" {{ old('invoice_payment_method', $parish->invoice_payment_method) == 'Virement bancaire' ? 'selected' : '' }}>Virement bancaire</option>
                                            <option value="Mobile Money" {{ old('invoice_payment_method', $parish->invoice_payment_method) == 'Mobile Money' ? 'selected' : '' }}>Mobile Money</option>
                                            <option value="Chèque" {{ old('invoice_payment_method', $parish->invoice_payment_method) == 'Chèque' ? 'selected' : '' }}>Chèque</option>
                                            <option value="Mixte" {{ old('invoice_payment_method', $parish->invoice_payment_method) == 'Mixte' ? 'selected' : '' }}>Mixte</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Mentions légales *</label>
                                        <textarea name="invoice_legal_mentions" class="form-control" rows="4" required 
                                                  placeholder="Mentions légales obligatoires (séparées par des points)">{{ old('invoice_legal_mentions', $parish->invoice_legal_mentions) }}</textarea>
                                        <small class="text-muted">Chaque phrase sera affichée comme une puce séparée</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="row">
                <div class="col-12">
                    <div class="parocompta-card p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Les champs marqués d'un * sont obligatoires
                                </small>
                            </div>
                            <div>
                                <a href="{{ route('payments.index') }}" class="btn btn-secondary me-2">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    Retour aux paiements
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>
                                    Enregistrer la configuration
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
