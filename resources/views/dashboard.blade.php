<x-app-layout>
    <x-slot name="title">Tableau de bord</x-slot>

    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h3 mb-1 fw-bold text-dark">
                    <i class="fas fa-tachometer-alt me-2 text-primary"></i>
                    Tableau de bord
                </h2>
                <p class="text-muted mb-0">Vue d'ensemble de votre activité comptable</p>
            </div>
            <div>
                <a href="{{ route('entries.create') }}" class="btn btn-primary-custom">
                    <i class="fas fa-plus me-2"></i>
                    Nouvelle fiche
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Notification importante sur la nature de l'application -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info border-0 shadow-sm" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-info-circle fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h5 class="alert-heading mb-2 fw-bold text-primary">
                            <i class="fas fa-church me-2"></i>
                            Application de Suivi des Recettes Paroissiales
                        </h5>
                        <p class="mb-1">
                            <strong>ParoCompta</strong> est spécialement conçu pour le <strong>suivi des recettes et quêtes paroissiales</strong> uniquement.
                        </p>
                        <p class="mb-0 small text-muted">
                            <i class="fas fa-exclamation-triangle me-1 text-warning"></i>
                            Cette application ne constitue pas un système de comptabilité paroissiale complète et ne remplace pas un logiciel comptable professionnel.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques principales -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="parocompta-card p-4 text-center">
                <div class="text-primary mb-2">
                    <i class="fas fa-file-alt fa-2x"></i>
                </div>
                <h4 class="fw-bold mb-1">{{ $stats['total_entries'] ?? 0 }}</h4>
                <p class="text-muted mb-0">Fiches créées</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="parocompta-card p-4 text-center">
                <div class="text-success mb-2">
                    <i class="fas fa-church fa-2x"></i>
                </div>
                <h4 class="fw-bold mb-1">{{ $stats['total_parishes'] ?? 0 }}</h4>
                <p class="text-muted mb-0">Paroisses actives</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="parocompta-card p-4 text-center">
                <div class="text-warning mb-2">
                    <i class="fas fa-calendar-week fa-2x"></i>
                </div>
                <h4 class="fw-bold mb-1">{{ $stats['current_month_entries'] ?? 0 }}</h4>
                <p class="text-muted mb-0">Ce mois-ci</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="parocompta-card p-4 text-center">
                <div class="text-info mb-2">
                    <i class="fas fa-coins fa-2x"></i>
                </div>
                <h4 class="fw-bold mb-1">{{ number_format($stats['total_amount'] ?? 0, 0, ',', ' ') }} FCFA</h4>
                <p class="text-muted mb-0">Montant total</p>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="parocompta-card p-4">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-bolt me-2 text-warning"></i>
                    Actions rapides
                </h5>
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('entries.create') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-plus me-2"></i>
                            Nouvelle fiche
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('entries.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-list me-2"></i>
                            Voir toutes les fiches
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('parishes.index') }}" class="btn btn-outline-success w-100">
                            <i class="fas fa-church me-2"></i>
                            Gérer les paroisses
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('payments.index') }}" class="btn btn-outline-info w-100">
                            <i class="fas fa-coins me-2"></i>
                            Suivi des paiements
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fiches récentes -->
    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="parocompta-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-clock me-2 text-primary"></i>
                        Fiches récentes
                    </h5>
                    <a href="{{ route('entries.index') }}" class="btn btn-sm btn-outline-primary">
                        Voir tout
                    </a>
                </div>

                @if(isset($recent_entries) && count($recent_entries) > 0)
                <div class="table-responsive">
                    <table class="table excel-table mb-0">
                        <thead>
                            <tr>
                                <th>Paroisse</th>
                                <th>Catégorie</th>
                                <th>Semaine</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent_entries as $entry)
                            <tr>
                                <td>{{ $entry->parish->name }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $entry->category->name }}</span>
                                </td>
                                <td>{{ $entry->week_label }}</td>
                                <td>{{ $entry->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('entries.show', $entry) }}" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($entry->generated_file)
                                    <a href="{{ route('entries.download', $entry) }}" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucune fiche créée pour le moment</p>
                    <a href="{{ route('entries.create') }}" class="btn btn-primary-custom">
                        <i class="fas fa-plus me-2"></i>
                        Créer votre première fiche
                    </a>
                </div>
                @endif
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="parocompta-card p-4">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-chart-pie me-2 text-success"></i>
                    Répartition par catégorie
                </h5>

                @if(isset($categories_stats) && count($categories_stats) > 0)
                <div class="space-y-3">
                    @foreach($categories_stats as $stat)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-sm">{{ $stat['name'] }}</span>
                        <span class="badge bg-secondary">{{ $stat['count'] }}</span>
                    </div>
                    <div class="progress mb-3" style="height: 6px;">
                        <div class="progress-bar bg-primary" style="width: {{ $stat['percentage'] }}%"></div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-3">
                    <i class="fas fa-chart-pie fa-2x text-muted mb-2"></i>
                    <p class="text-muted small">Aucune donnée disponible</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
