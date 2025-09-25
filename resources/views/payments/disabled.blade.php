<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-bold text-primary">
            <i class="fas fa-euro-sign me-2"></i>
            Suivi des Paiements
        </h2>
    </x-slot>

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="parocompta-card p-5 text-center">
                    <div class="mb-4">
                        <i class="fas fa-ban fa-4x text-muted mb-3"></i>
                        <h4 class="fw-bold text-muted">Module de Suivi des Paiements Désactivé</h4>
                    </div>
                    
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Le suivi des paiements n'est pas activé pour votre paroisse.
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-muted">
                            Pour activer le suivi des paiements, contactez votre administrateur 
                            ou activez cette fonctionnalité dans les paramètres de votre paroisse.
                        </p>
                    </div>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('dashboard') }}" class="btn btn-primary-custom">
                            <i class="fas fa-home me-2"></i>
                            Retour au tableau de bord
                        </a>
                        
                        @if(auth()->user()->role === 'admin')
                        <a href="{{ route('parishes.edit', auth()->user()->parish) }}" class="btn btn-outline-primary">
                            <i class="fas fa-cog me-2"></i>
                            Paramètres de la paroisse
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
