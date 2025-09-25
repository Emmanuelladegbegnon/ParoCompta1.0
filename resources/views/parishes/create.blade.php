<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-church mr-2"></i> Nouvelle paroisse
            </h2>
            <a href="{{ route('parishes.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg shadow-md transition duration-300 ease-in-out flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
            </a>
        </div>
    </x-slot>
    
    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg sm:rounded-lg overflow-hidden">
                <div class="p-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b">
                    <h3 class="text-lg font-medium text-gray-800">Informations de la paroisse</h3>
                </div>
                
                <div class="p-6">
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                            <p class="font-bold">Veuillez corriger les erreurs suivantes :</p>
                            <ul class="list-disc ml-4">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('parishes.store') }}" class="space-y-6" id="parishForm">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nom de la paroisse <span class="text-red-500">*</span></label>
                                <input id="name" name="name" value="{{ old('name') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required />
                                <p class="mt-1 text-xs text-gray-500">Entrez le nom complet de la paroisse</p>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-folder me-2"></i>
                                    Dossier de stockage
                                </label>
                                <div class="input-group">
                                    <input type="text" name="storage_path" id="storage_path"
                                           class="form-control @error('storage_path') is-invalid @enderror"
                                           value="{{ old('storage_path', 'C:\\ParoCompta\\Documents') }}"
                                           placeholder="C:\ParoCompta\Documents\MaParoisse">
                                    <button type="button" class="btn btn-outline-secondary" onclick="selectFolder()">
                                        <i class="fas fa-folder-open me-2"></i>
                                        Parcourir
                                    </button>
                                </div>
                                @error('storage_path')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Dossier local où seront stockés les fichiers Word générés
                                </small>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-4">
                            <h4 class="text-md font-medium text-gray-700 mb-3">Configuration des paiements</h4>
                            
                            <div class="flex items-start mb-4">
                                <div class="flex items-center h-5">
                                    <input id="enable_payment_tracking" name="enable_payment_tracking" type="checkbox" value="1" {{ old('enable_payment_tracking') ? 'checked' : '' }} class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" />
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="enable_payment_tracking" class="font-medium text-gray-700">Activer le suivi des paiements</label>
                                    <p class="text-gray-500">Permet de suivre les paiements hebdomadaires pour cette paroisse</p>
                                </div>
                            </div>
                            
                            <div id="paymentSection" class="ml-7 pl-2 border-l-2 border-blue-200 {{ old('enable_payment_tracking') ? '' : 'opacity-50' }}">
                                <div class="mb-4">
                                    <label for="weekly_payment_amount" class="block text-sm font-medium text-gray-700">Montant hebdomadaire (€)</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">€</span>
                                        </div>
                                        <input type="number" step="0.01" min="0" id="weekly_payment_amount" name="weekly_payment_amount" value="{{ old('weekly_payment_amount', '0.00') }}" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="0.00" {{ old('enable_payment_tracking') ? '' : 'disabled' }} />
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">EUR</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-3 pt-5 border-t border-gray-200">
                            <a href="{{ route('parishes.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Annuler
                            </a>
                            <button type="submit" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-save mr-2"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        // Gestion dynamique de l'activation/désactivation du suivi des paiements
        document.addEventListener('DOMContentLoaded', function() {
            const enablePaymentCheckbox = document.getElementById('enable_payment_tracking');
            const paymentSection = document.getElementById('paymentSection');
            const weeklyPaymentInput = document.getElementById('weekly_payment_amount');
            
            function togglePaymentSection() {
                if (enablePaymentCheckbox.checked) {
                    paymentSection.classList.remove('opacity-50');
                    weeklyPaymentInput.removeAttribute('disabled');
                } else {
                    paymentSection.classList.add('opacity-50');
                    weeklyPaymentInput.setAttribute('disabled', 'disabled');
                }
            }
            
            enablePaymentCheckbox.addEventListener('change', togglePaymentSection);
            togglePaymentSection(); // État initial

            // Validation du formulaire
            document.getElementById('parishForm').addEventListener('submit', function(e) {
                const nameInput = document.getElementById('name');
                if (!nameInput.value.trim()) {
                    e.preventDefault();
                    nameInput.classList.add('border-red-500');
                    alert('Le nom de la paroisse est obligatoire.');
                }
            });
        });

        // Fonction pour sélectionner un dossier
        function selectFolder() {
            // Pour une vraie application, ceci nécessiterait une API backend
            // ou une extension de navigateur pour accéder au système de fichiers
            const currentPath = document.getElementById('storage_path').value;
            const newPath = prompt('Entrez le chemin complet du dossier de stockage:', currentPath);
            if (newPath && newPath.trim()) {
                document.getElementById('storage_path').value = newPath.trim();
            }
        }
        });
    </script>
    @endpush
</x-app-layout>



