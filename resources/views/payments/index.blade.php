<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-bold text-primary">
            <i class="fas fa-coins me-2"></i>
            Suivi des Paiements - {{ $parish->name }}
        </h2>
    </x-slot>

    <div class="container-fluid py-4">
        <!-- Sélecteur de mois -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="parocompta-card p-3">
                    <form method="GET" class="row align-items-end">
                        @if(auth()->user()->role === 'admin')
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Paroisse</label>
                            <select name="parish_id" class="form-select">
                                @foreach($parishes as $p)
                                <option value="{{ $p->id }}" {{ $parish->id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Année</label>
                            <select name="year" class="form-select">
                                @for($y = now()->year; $y >= now()->year - 2; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Mois</label>
                            <select name="month" class="form-select">
                                @foreach(['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'] as $index => $monthName)
                                <option value="{{ $index + 1 }}" {{ $month == ($index + 1) ? 'selected' : '' }}>{{ $monthName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="fas fa-search me-2"></i>
                                Afficher
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        <!-- Statistiques cumulées globales -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="parocompta-card p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-chart-bar me-2 text-success"></i>
                        Statistiques cumulées - Tous les paiements
                    </h5>

                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                                <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                                <h6 class="fw-bold text-muted small">TOTAL REÇU</h6>
                                <h4 class="fw-bold text-success mb-0">{{ number_format($cumulativeStats['total_received'], 0, ',', ' ') }} FCFA</h4>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="text-center p-3 bg-primary bg-opacity-10 rounded">
                                <i class="fas fa-calculator fa-2x text-primary mb-2"></i>
                                <h6 class="fw-bold text-muted small">TOTAL DÛ</h6>
                                <h4 class="fw-bold text-primary mb-0">{{ number_format($cumulativeStats['total_due'], 0, ',', ' ') }} FCFA</h4>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="text-center p-3 {{ $cumulativeStats['total_balance'] <= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 rounded">
                                <i class="fas fa-balance-scale fa-2x {{ $cumulativeStats['total_balance'] <= 0 ? 'text-success' : 'text-danger' }} mb-2"></i>
                                <h6 class="fw-bold text-muted small">SOLDE RESTANT</h6>
                                <h4 class="fw-bold {{ $cumulativeStats['total_balance'] <= 0 ? 'text-success' : 'text-danger' }} mb-0">
                                    {{ number_format($cumulativeStats['total_balance'], 0, ',', ' ') }} F
                                </h4>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="text-center p-3 bg-info bg-opacity-10 rounded">
                                <i class="fas fa-calendar-week fa-2x text-info mb-2"></i>
                                <h6 class="fw-bold text-muted small">SEMAINES TOTAL</h6>
                                <h4 class="fw-bold text-info mb-0">{{ $cumulativeStats['total_weeks'] }}</h4>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="text-center p-3 bg-warning bg-opacity-10 rounded">
                                <i class="fas fa-chart-line fa-2x text-warning mb-2"></i>
                                <h6 class="fw-bold text-muted small">MOYENNE/MOIS</h6>
                                <h4 class="fw-bold text-warning mb-0">{{ number_format($cumulativeStats['average_per_month'], 0, ',', ' ') }} FCFA</h4>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="text-center p-3 bg-secondary bg-opacity-10 rounded">
                                <i class="fas fa-percentage fa-2x text-secondary mb-2"></i>
                                <h6 class="fw-bold text-muted small">TAUX PAIEMENT</h6>
                                <h4 class="fw-bold text-secondary mb-0">{{ number_format($cumulativeStats['completion_rate'], 1) }}%</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques détaillées -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-muted">
                                <i class="fas fa-trophy me-2"></i>
                                Meilleur mois
                            </h6>
                            @if($cumulativeStats['best_month'])
                            <p class="mb-0">
                                <span class="badge bg-success">
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $cumulativeStats['best_month']->period)->locale('fr')->isoFormat('MMMM YYYY') }}
                                </span>
                                <span class="fw-bold text-success ms-2">
                                    {{ number_format($cumulativeStats['best_month']->total, 0, ',', ' ') }} F
                                </span>
                            </p>
                            @else
                            <p class="text-muted mb-0">Aucun paiement enregistré</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-muted">
                                <i class="fas fa-coins me-2"></i>
                                Moyenne par semaine
                            </h6>
                            <p class="mb-0">
                                <span class="fw-bold text-info">
                                    {{ number_format($cumulativeStats['average_per_week'], 0, ',', ' ') }} F
                                </span>
                                <small class="text-muted">par semaine travaillée</small>
                            </p>
                        </div>
                    </div>

                    <!-- Lien vers les statistiques détaillées -->
                    <div class="text-center mt-3">
                        <a href="{{ route('stats.index') }}" class="btn btn-outline-info">
                            <i class="fas fa-chart-line me-2"></i>
                            Voir les statistiques détaillées
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques du mois sélectionné -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="parocompta-card p-4 text-center">
                    <i class="fas fa-calendar-week fa-2x text-primary mb-2"></i>
                    <h6 class="fw-bold text-muted">Semaines saisies</h6>
                    <h3 class="fw-bold text-primary">{{ $monthData['weeks_worked'] }}</h3>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="parocompta-card p-4 text-center">
                    <i class="fas fa-coins fa-2x text-warning mb-2"></i>
                    <h6 class="fw-bold text-muted">Montant par semaine</h6>
                    <h3 class="fw-bold text-warning">{{ number_format($monthData['weekly_amount'], 0, ',', ' ') }} FCFA</h3>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="parocompta-card p-4 text-center">
                    <i class="fas fa-calculator fa-2x text-success mb-2"></i>
                    <h6 class="fw-bold text-muted">Montant dû</h6>
                    <h3 class="fw-bold text-success">{{ number_format($monthData['amount_due'], 0, ',', ' ') }} FCFA</h3>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="parocompta-card p-4 text-center">
                    <i class="fas fa-balance-scale fa-2x {{ $monthData['amount_due'] - $totalPaid <= 0 ? 'text-success' : 'text-danger' }} mb-2"></i>
                    <h6 class="fw-bold text-muted">Solde restant</h6>
                    <h3 class="fw-bold {{ $monthData['amount_due'] - $totalPaid <= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($monthData['amount_due'] - $totalPaid, 0, ',', ' ') }} F
                    </h3>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Formulaire d'enregistrement de paiement -->
            <div class="col-md-6 mb-4">
                <div class="parocompta-card p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-plus-circle me-2 text-primary"></i>
                        Enregistrer un paiement
                    </h5>

                    @if($monthData['weeks_worked'] > 0)
                    <form method="POST" action="{{ route('payments.store') }}">
                        @csrf
                        <input type="hidden" name="period" value="{{ $period }}">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Montant reçu (FCFA)</label>
                            <input type="number" name="amount_received" class="form-control @error('amount_received') is-invalid @enderror"
                                   value="{{ old('amount_received') }}" min="0" step="1" required>
                            @error('amount_received')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Date de paiement</label>
                            <input type="date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror"
                                   value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>
                            @error('payment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mode de paiement</label>
                            <select name="payment_method" class="form-select">
                                <option value="">-- Sélectionner --</option>
                                <option value="Espèces" {{ old('payment_method') == 'Espèces' ? 'selected' : '' }}>Espèces</option>
                                <option value="Virement" {{ old('payment_method') == 'Virement' ? 'selected' : '' }}>Virement</option>
                                <option value="Chèque" {{ old('payment_method') == 'Chèque' ? 'selected' : '' }}>Chèque</option>
                                <option value="Mobile Money" {{ old('payment_method') == 'Mobile Money' ? 'selected' : '' }}>Mobile Money</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Notes optionnelles...">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary-custom w-100">
                            <i class="fas fa-save me-2"></i>
                            Enregistrer le paiement
                        </button>

                        <div class="mt-3 p-2 bg-light rounded">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                <strong>Factures :</strong> Une fois le paiement enregistré, vous pourrez générer une facture normalisée automatiquement.
                            </small>
                        </div>
                    </form>
                    @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Aucune fiche saisie pour {{ \Carbon\Carbon::createFromFormat('Y-m', $period)->locale('fr')->isoFormat('MMMM YYYY') }}.
                    </div>
                    @endif
                </div>
            </div>

            <!-- Historique des paiements du mois -->
            <div class="col-md-6 mb-4">
                <div class="parocompta-card p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-history me-2 text-success"></i>
                        Paiements de {{ \Carbon\Carbon::createFromFormat('Y-m', $period)->locale('fr')->isoFormat('MMMM YYYY') }}
                    </h5>

                    @if($payments->count() > 0)
                    <div class="table-responsive">
                        <table class="table excel-table mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Montant</th>
                                    <th>Mode</th>
                                    <th>Facture</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                    <td class="fw-bold text-success">{{ number_format($payment->amount_received, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        @if($payment->payment_method)
                                        <span class="badge bg-secondary">{{ $payment->payment_method }}</span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->hasInvoice())
                                        <span class="badge bg-success">
                                            <i class="fas fa-file-invoice me-1"></i>
                                            {{ $payment->invoice_number }}
                                        </span>
                                        @else
                                        <span class="text-muted">
                                            <i class="fas fa-minus"></i>
                                            Aucune
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @if($payment->hasInvoice())
                                            <!-- Télécharger la facture -->
                                            <a href="{{ route('payments.download-invoice', $payment) }}"
                                               class="btn btn-outline-success"
                                               title="Télécharger la facture">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            @else
                                            <!-- Générer la facture -->
                                            <form method="POST" action="{{ route('payments.generate-invoice', $payment) }}" class="d-inline">
                                                @csrf
                                                <button type="submit"
                                                        class="btn btn-outline-primary"
                                                        title="Générer la facture"
                                                        onclick="return confirm('Générer une facture pour ce paiement ?')">
                                                    <i class="fas fa-file-invoice"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-success">
                                    <th>Total payé</th>
                                    <th class="fw-bold">{{ number_format($totalPaid, 0, ',', ' ') }} FCFA</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-3">
                        <i class="fas fa-receipt fa-2x text-muted mb-2"></i>
                        <p class="text-muted">Aucun paiement enregistré pour ce mois</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Historique des 6 derniers mois -->
        <div class="row">
            <div class="col-12">
                <div class="parocompta-card p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-chart-line me-2 text-info"></i>
                        Historique des 6 derniers mois
                    </h5>

                    <div class="table-responsive">
                        <table class="table excel-table mb-0">
                            <thead>
                                <tr>
                                    <th>Période</th>
                                    <th>Semaines</th>
                                    <th>Montant dû</th>
                                    <th>Montant payé</th>
                                    <th>Solde</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthsHistory as $historyMonth)
                                <tr class="{{ $historyMonth['period'] == $period ? 'table-primary' : '' }}">
                                    <td class="fw-bold">{{ $historyMonth['period_name'] }}</td>
                                    <td>{{ $historyMonth['weeks_worked'] }}</td>
                                    <td>{{ number_format($historyMonth['amount_due'], 0, ',', ' ') }} FCFA</td>
                                    <td class="text-success fw-bold">{{ number_format($historyMonth['amount_paid'], 0, ',', ' ') }} FCFA</td>
                                    <td class="{{ $historyMonth['remaining_balance'] <= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                        {{ number_format($historyMonth['remaining_balance'], 0, ',', ' ') }} F
                                    </td>
                                    <td>
                                        @if($historyMonth['is_fully_paid'])
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>
                                            Payé
                                        </span>
                                        @elseif($historyMonth['amount_paid'] > 0)
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>
                                            Partiel
                                        </span>
                                        @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times me-1"></i>
                                            Impayé
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

        <!-- Statistiques par année -->
        @if($cumulativeStats['yearly_stats']->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="parocompta-card p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-calendar-alt me-2 text-warning"></i>
                        Récapitulatif par année
                    </h5>

                    <div class="table-responsive">
                        <table class="table excel-table mb-0">
                            <thead>
                                <tr>
                                    <th>
                                        <i class="fas fa-calendar me-2"></i>
                                        Année
                                    </th>
                                    <th>
                                        <i class="fas fa-money-bill-wave me-2"></i>
                                        Montant reçu
                                    </th>
                                    <th>
                                        <i class="fas fa-receipt me-2"></i>
                                        Nombre de paiements
                                    </th>
                                    <th>
                                        <i class="fas fa-chart-line me-2"></i>
                                        Moyenne mensuelle
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cumulativeStats['yearly_stats'] as $yearStat)
                                <tr>
                                    <td class="fw-bold">{{ $yearStat->year }}</td>
                                    <td class="fw-bold text-success">
                                        {{ number_format($yearStat->total_received, 0, ',', ' ') }} F
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $yearStat->payment_count }}</span>
                                    </td>
                                    <td class="text-info">
                                        {{ number_format($yearStat->total_received / 12, 0, ',', ' ') }} F
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-success">
                                    <th>TOTAL GÉNÉRAL</th>
                                    <th class="fw-bold">{{ number_format($cumulativeStats['total_received'], 0, ',', ' ') }} FCFA</th>
                                    <th class="fw-bold">{{ $cumulativeStats['yearly_stats']->sum('payment_count') }}</th>
                                    <th class="fw-bold">{{ number_format($cumulativeStats['average_per_month'], 0, ',', ' ') }} FCFA</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>



