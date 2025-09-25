<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-bold text-primary">
            <i class="fas fa-chart-line me-2"></i>
            Statistiques détaillées - {{ $user->name }}
        </h2>
    </x-slot>

    <div class="container-fluid py-4">
        <!-- Statistiques générales -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="parocompta-card p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-trophy me-2 text-warning"></i>
                        Résumé général de vos paiements
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                                <i class="fas fa-money-bill-wave fa-3x text-success mb-2"></i>
                                <h6 class="fw-bold text-muted small">TOTAL REÇU</h6>
                                <h3 class="fw-bold text-success mb-0">{{ number_format($totalStats['total_received'], 0, ',', ' ') }} FCFA</h3>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 bg-primary bg-opacity-10 rounded">
                                <i class="fas fa-calculator fa-3x text-primary mb-2"></i>
                                <h6 class="fw-bold text-muted small">TOTAL DÛ</h6>
                                <h3 class="fw-bold text-primary mb-0">{{ number_format($totalStats['total_due'], 0, ',', ' ') }} FCFA</h3>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 {{ $totalStats['total_balance'] <= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 rounded">
                                <i class="fas fa-balance-scale fa-3x {{ $totalStats['total_balance'] <= 0 ? 'text-success' : 'text-danger' }} mb-2"></i>
                                <h6 class="fw-bold text-muted small">SOLDE RESTANT</h6>
                                <h3 class="fw-bold {{ $totalStats['total_balance'] <= 0 ? 'text-success' : 'text-danger' }} mb-0">
                                    {{ number_format($totalStats['total_balance'], 0, ',', ' ') }} F
                                </h3>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 bg-info bg-opacity-10 rounded">
                                <i class="fas fa-percentage fa-3x text-info mb-2"></i>
                                <h6 class="fw-bold text-muted small">TAUX PAIEMENT</h6>
                                <h3 class="fw-bold text-info mb-0">{{ number_format($totalStats['completion_rate'], 1) }}%</h3>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <h6 class="fw-bold text-muted">
                                <i class="fas fa-calendar-week me-2"></i>
                                Semaines travaillées
                            </h6>
                            <p class="mb-0">
                                <span class="fw-bold text-primary fs-4">{{ $totalStats['total_weeks'] }}</span>
                                <small class="text-muted">semaines</small>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="fw-bold text-muted">
                                <i class="fas fa-coins me-2"></i>
                                Moyenne par semaine
                            </h6>
                            <p class="mb-0">
                                <span class="fw-bold text-success fs-4">{{ number_format($totalStats['average_per_week'], 0, ',', ' ') }} FCFA</span>
                                <small class="text-muted">par semaine</small>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="fw-bold text-muted">
                                <i class="fas fa-chart-line me-2"></i>
                                Moyenne par mois
                            </h6>
                            <p class="mb-0">
                                <span class="fw-bold text-warning fs-4">{{ number_format($totalStats['average_per_month'], 0, ',', ' ') }} FCFA</span>
                                <small class="text-muted">par mois</small>
                            </p>
                        </div>
                    </div>
                    
                    @if($totalStats['first_work_date'] && $totalStats['last_work_date'])
                    <div class="row mt-3 pt-3 border-top">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-muted">
                                <i class="fas fa-play me-2"></i>
                                Première semaine travaillée
                            </h6>
                            <p class="mb-0">
                                <span class="badge bg-primary">
                                    {{ \Carbon\Carbon::parse($totalStats['first_work_date'])->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-muted">
                                <i class="fas fa-stop me-2"></i>
                                Dernière semaine travaillée
                            </h6>
                            <p class="mb-0">
                                <span class="badge bg-secondary">
                                    {{ \Carbon\Carbon::parse($totalStats['last_work_date'])->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                                </span>
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Comparaison avec les objectifs -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="parocompta-card p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-target me-2 text-danger"></i>
                        Objectifs {{ \Carbon\Carbon::now()->year }}
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 bg-warning bg-opacity-10 rounded">
                                <i class="fas fa-bullseye fa-2x text-warning mb-2"></i>
                                <h6 class="fw-bold text-muted small">OBJECTIF AJUSTÉ</h6>
                                <h4 class="fw-bold text-warning mb-0">{{ number_format($goalComparison['adjusted_goal'], 0, ',', ' ') }} FCFA</h4>
                                <small class="text-muted">{{ $goalComparison['weeks_worked_this_year'] }} semaines</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <h6 class="fw-bold text-muted small">REÇU EN {{ \Carbon\Carbon::now()->year }}</h6>
                                <h4 class="fw-bold text-success mb-0">{{ number_format($goalComparison['yearly_received'], 0, ',', ' ') }} FCFA</h4>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 bg-info bg-opacity-10 rounded">
                                <i class="fas fa-percentage fa-2x text-info mb-2"></i>
                                <h6 class="fw-bold text-muted small">PROGRESSION</h6>
                                <h4 class="fw-bold text-info mb-0">{{ number_format($goalComparison['goal_completion'], 1) }}%</h4>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 bg-danger bg-opacity-10 rounded">
                                <i class="fas fa-hourglass-half fa-2x text-danger mb-2"></i>
                                <h6 class="fw-bold text-muted small">RESTE À RECEVOIR</h6>
                                <h4 class="fw-bold text-danger mb-0">{{ number_format($goalComparison['remaining_to_goal'], 0, ',', ' ') }} FCFA</h4>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Barre de progression -->
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-bold">Progression vers l'objectif</span>
                            <span class="fw-bold">{{ number_format($goalComparison['goal_completion'], 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar 
                                @if($goalComparison['goal_completion'] >= 100) bg-success
                                @elseif($goalComparison['goal_completion'] >= 75) bg-warning
                                @else bg-danger
                                @endif" 
                                role="progressbar" 
                                style="width: {{ min(100, $goalComparison['goal_completion']) }}%">
                                {{ number_format($goalComparison['goal_completion'], 1) }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Détail mensuel -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="parocompta-card p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-calendar-alt me-2 text-info"></i>
                        Détail par mois
                    </h5>
                    
                    <div class="table-responsive">
                        <table class="table excel-table mb-0">
                            <thead>
                                <tr>
                                    <th>
                                        <i class="fas fa-calendar me-2"></i>
                                        Période
                                    </th>
                                    <th>
                                        <i class="fas fa-calendar-week me-2"></i>
                                        Semaines
                                    </th>
                                    <th>
                                        <i class="fas fa-calculator me-2"></i>
                                        Montant dû
                                    </th>
                                    <th>
                                        <i class="fas fa-money-bill-wave me-2"></i>
                                        Montant reçu
                                    </th>
                                    <th>
                                        <i class="fas fa-balance-scale me-2"></i>
                                        Solde
                                    </th>
                                    <th>
                                        <i class="fas fa-percentage me-2"></i>
                                        Taux
                                    </th>
                                    <th>
                                        <i class="fas fa-receipt me-2"></i>
                                        Paiements
                                    </th>
                                    <th>
                                        <i class="fas fa-info-circle me-2"></i>
                                        Statut
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlyStats as $month)
                                <tr>
                                    <td class="fw-bold">
                                        {{ \Carbon\Carbon::createFromFormat('Y-m', $month['period'])->locale('fr')->isoFormat('MMMM YYYY') }}
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $month['weeks_worked'] }}</span>
                                    </td>
                                    <td class="fw-bold text-primary">
                                        {{ number_format($month['amount_due'], 0, ',', ' ') }} F
                                    </td>
                                    <td class="fw-bold text-success">
                                        {{ number_format($month['total_received'], 0, ',', ' ') }} F
                                    </td>
                                    <td class="fw-bold {{ $month['balance'] <= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($month['balance'], 0, ',', ' ') }} F
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @if($month['completion_rate'] >= 100) bg-success
                                            @elseif($month['completion_rate'] >= 50) bg-warning
                                            @else bg-danger
                                            @endif">
                                            {{ number_format($month['completion_rate'], 1) }}%
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $month['payment_count'] }}</span>
                                    </td>
                                    <td>
                                        @if($month['balance'] <= 0)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Payé
                                            </span>
                                        @elseif($month['total_received'] > 0)
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock me-1"></i>Partiel
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times me-1"></i>Impayé
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="row">
            <div class="col-12 text-center">
                <a href="{{ route('payments.index') }}" class="btn btn-primary-custom me-2">
                    <i class="fas fa-arrow-left me-2"></i>
                    Retour aux paiements
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-home me-2"></i>
                    Tableau de bord
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
