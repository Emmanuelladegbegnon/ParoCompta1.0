<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-bold text-primary">
            <i class="fas fa-coins me-2"></i>
            Suivi des Paiements
        </h2>
    </x-slot>

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="parocompta-card p-5 text-center">
                    <div class="mb-4">
                        <i class="fas fa-church fa-4x text-muted"></i>
                    </div>
                    
                    <h3 class="fw-bold text-dark mb-3">Aucune paroisse disponible</h3>
                    
                    <p class="text-muted mb-4">
                        Il n'y a actuellement aucune paroisse enregistrée dans le système.
                        <br>
                        Vous devez d'abord créer une paroisse pour pouvoir accéder au suivi des paiements.
                    </p>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('parishes.create') }}" class="btn btn-primary-custom">
                            <i class="fas fa-plus me-2"></i>
                            Créer une paroisse
                        </a>
                        
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Retour au tableau de bord
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
