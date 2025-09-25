<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-bold text-primary">
            <i class="fas fa-church me-2"></i>
            Gestion des Paroisses
        </h2>
    </x-slot>

    <div class="container-fluid py-4">
        @if (session('status') || session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('status') ?? session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Actions rapides -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="parocompta-card p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-list me-2 text-primary"></i>
                            Liste des Paroisses
                        </h5>
                        <a href="{{ route('parishes.create') }}" class="btn btn-primary-custom">
                            <i class="fas fa-plus-circle me-2"></i>
                            Nouvelle paroisse
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des paroisses -->
        <div class="row">
            <div class="col-12">
                <div class="parocompta-card p-4">
                    <!-- Barre de recherche -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" id="searchParish" class="form-control" placeholder="Rechercher une paroisse...">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table excel-table mb-0" id="parishesTable">
                            <thead>
                                <tr>
                                    <th>
                                        <i class="fas fa-church me-2"></i>
                                        Nom
                                    </th>
                                    <th>
                                        <i class="fas fa-euro-sign me-2"></i>
                                        Suivi des paiements
                                    </th>
                                    <th>
                                        <i class="fas fa-coins me-2"></i>
                                        Montant hebdomadaire
                                    </th>
                                    <th>
                                        <i class="fas fa-cogs me-2"></i>
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($parishes as $parish)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-church text-primary"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $parish->name }}</div>
                                                <small class="text-muted">{{ $parish->users_count ?? 0 }} utilisateur(s)</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($parish->enable_payment_tracking)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>
                                                Activé
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle me-1"></i>
                                                Désactivé
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">
                                            {{ number_format($parish->weekly_payment_amount ?? 0, 0, ',', ' ') }} FCFA
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('parishes.files', $parish) }}" class="btn btn-sm btn-outline-success" title="Voir les fichiers">
                                                <i class="fas fa-folder-open"></i>
                                            </a>
                                            <a href="{{ route('parishes.edit', $parish) }}" class="btn btn-sm btn-outline-primary" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('parishes.categories.index', $parish) }}" class="btn btn-sm btn-outline-info" title="Catégories">
                                                <i class="fas fa-list-alt"></i>
                                            </a>
                                            <form action="{{ route('parishes.destroy', $parish) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="if(confirm('Supprimer la paroisse {{ $parish->name }} ?')) this.form.submit()">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($parishes->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $parishes->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Fonction de recherche dynamique
        document.getElementById('searchParish').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const table = document.getElementById('parishesTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const nameCell = rows[i].getElementsByTagName('td')[0];
                if (nameCell) {
                    const textValue = nameCell.textContent || nameCell.innerText;
                    if (textValue.toLowerCase().indexOf(searchValue) > -1) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>



