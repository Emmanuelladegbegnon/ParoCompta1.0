<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-bold text-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Configuration des Factures
        </h2>
    </x-slot>

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="parocompta-card text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-church text-warning" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h3 class="text-warning mb-3">Aucune paroisse disponible</h3>
                    
                    <p class="text-muted mb-4">
                        Il n'y a actuellement aucune paroisse dans le système.<br>
                        Vous devez d'abord créer une paroisse avant de pouvoir configurer les factures.
                    </p>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('parishes.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Créer une paroisse
                        </a>
                        
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-home me-2"></i>
                            Retour au tableau de bord
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
