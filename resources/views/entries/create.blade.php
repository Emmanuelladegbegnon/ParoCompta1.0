<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-file-alt mr-2"></i> Nouvelle fiche
            </h2>
            <a href="{{ route('entries.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg shadow-md transition duration-300 ease-in-out flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
            </a>
        </div>
    </x-slot>
    
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg sm:rounded-lg overflow-hidden">
                <div class="p-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b">
                    <h3 class="text-lg font-medium text-gray-800">Informations de la fiche</h3>
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
                    
                    <form method="POST" action="{{ route('entries.store') }}" enctype="multipart/form-data" class="space-y-6" id="entryForm">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="parish_id" class="block text-sm font-medium text-gray-700">Paroisse <span class="text-red-500">*</span></label>
                                <select id="parish_id" name="parish_id" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                                    <option value="">Sélectionnez une paroisse</option>
                                    @foreach ($parishes as $parish)
                                        <option value="{{ $parish->id }}" {{ old('parish_id') == $parish->id ? 'selected' : '' }}>{{ $parish->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700">Catégorie <span class="text-red-500">*</span></label>
                                <select id="category_id" name="category_id" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                                    <option value="">Sélectionnez une catégorie</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="week_label" class="block text-sm font-medium text-gray-700">Libellé semaine <span class="text-red-500">*</span></label>
                                <input id="week_label" name="week_label" value="{{ old('week_label') }}" placeholder="ex: Avril S1" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required />
                            </div>
                            
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Date de début <span class="text-red-500">*</span></label>
                                <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required />
                            </div>
                            
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700">Date de fin <span class="text-red-500">*</span></label>
                                <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required />
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-4">
                            <h4 class="text-md font-medium text-gray-700 mb-3">Champs dynamiques</h4>
                            <div id="dynamic-fields" class="space-y-8 mt-2">
                                <div class="text-center text-gray-500">
                                    <i class="fas fa-info-circle mr-2"></i> Veuillez sélectionner une catégorie pour afficher les champs
                                </div>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-4">
                            <h4 class="text-md font-medium text-gray-700 mb-3">Document Word</h4>
                            
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="document" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Télécharger un fichier</span>
                                            <input id="document" name="document" type="file" accept=".doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" class="sr-only">
                                        </label>
                                        <p class="pl-1">ou glisser-déposer</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        Word (.doc, .docx) jusqu'à 10MB
                                    </p>
                                    <div id="file-name" class="text-sm text-gray-800 font-medium hidden mt-2">
                                        <i class="fas fa-file-word text-blue-500 mr-1"></i> <span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 text-sm text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i> Le document sera automatiquement généré si aucun fichier n'est téléchargé
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-3 pt-5 border-t border-gray-200">
                            <a href="{{ route('entries.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Annuler
                            </a>
                            <button type="submit" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-save mr-2"></i> Enregistrer et générer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('category_id');
            const dynamicFieldsContainer = document.getElementById('dynamic-fields');
            const fileInput = document.getElementById('document');
            const fileNameDisplay = document.getElementById('file-name');
            const fileNameText = fileNameDisplay.querySelector('span');
            
            // Gestion du téléchargement de fichier
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const fileName = this.files[0].name;
                    fileNameText.textContent = fileName;
                    fileNameDisplay.classList.remove('hidden');
                } else {
                    fileNameDisplay.classList.add('hidden');
                }
            });
            
            // Gestion des champs dynamiques
            function buildTable(headers) {
                const table = document.createElement('table');
                table.className = 'min-w-full divide-y divide-gray-200 border';
                const thead = document.createElement('thead');
                thead.className = 'bg-gray-50';
                const tr = document.createElement('tr');
                headers.forEach(h => {
                    const th = document.createElement('th');
                    th.className = 'px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider';
                    th.textContent = h;
                    tr.appendChild(th);
                });
                thead.appendChild(tr);
                const tbody = document.createElement('tbody');
                tbody.className = 'bg-white divide-y divide-gray-200';
                table.appendChild(thead);
                table.appendChild(tbody);
                return { table, tbody };
            }

            async function loadDynamicFields() {
                const categoryId = categorySelect.value;
                
                if (!categoryId) {
                    dynamicFieldsContainer.innerHTML = `
                        <div class="text-center text-gray-500">
                            <i class="fas fa-info-circle mr-2"></i> Veuillez sélectionner une catégorie pour afficher les champs
                        </div>
                    `;
                    return;
                }
                
                dynamicFieldsContainer.innerHTML = `
                    <div class="text-center">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                        <p class="mt-2 text-gray-600">Chargement des champs...</p>
                    </div>
                `;
                
                try {
                    const response = await fetch(`{{ url('categories') }}/${categoryId}/fields`);
                    const fields = await response.json();
                    
                    if (!fields || fields.length === 0) {
                        dynamicFieldsContainer.innerHTML = `
                            <div class="text-center text-gray-500">
                                <i class="fas fa-exclamation-circle mr-2"></i> Aucun champ défini pour cette catégorie
                            </div>
                        `;
                        return;
                    }
                    
                    dynamicFieldsContainer.innerHTML = '';

                    // Grouping logic
                    const map = new Map(fields.map(f => [f.name, f]));
                    const denomRows = [];
                    const otherRows = [];
                    const montantFields = [];
                    fields.forEach(f => {
                        const m = (f.name || '').match(/^(billets|pieces)_(\d+)_nombre$/i);
                        if (m) {
                            const base = m[1] + '_' + m[2];
                            const nombreName = base + '_nombre';
                            const montantName = base + '_montant';
                            const unit = map.get(nombreName)?.unit || parseFloat(m[2]);
                            denomRows.push({ label: (map.get(nombreName)?.label || base), nombreName, montantName, unit });
                            return;
                        }
                        const om = (f.name || '').match(/^(.*)_nombre$/);
                        if (om && !/^billets|pieces/i.test(om[1])) {
                            const base = om[1];
                            const nombreName = base + '_nombre';
                            const montantName = base + '_montant';
                            otherRows.push({ label: (map.get(nombreName)?.label || base), nombreName, montantName });
                            return;
                        }
                        if ((f.name || '').endsWith('_montant')) {
                            montantFields.push(f.name);
                        }
                    });

                    // Table 1: Billets & Pièces
                    if (denomRows.length) {
                        const h3 = document.createElement('h5');
                        h3.className = 'text-sm font-semibold text-gray-700';
                        h3.textContent = 'Billets & Pièces';
                        dynamicFieldsContainer.appendChild(h3);
                        const { table, tbody } = buildTable(['Type', 'Nombre', 'Montant']);
                        denomRows.forEach(row => {
                            const tr = document.createElement('tr');
                            const tdType = document.createElement('td'); tdType.className = 'px-4 py-2'; tdType.textContent = row.label;
                            const tdNombre = document.createElement('td'); tdNombre.className = 'px-4 py-2';
                            const inputNombre = document.createElement('input');
                            inputNombre.type = 'number'; inputNombre.min = '0'; inputNombre.step = '1';
                            inputNombre.name = `data[${row.nombreName}]`; inputNombre.className = 'w-full border rounded p-1';
                            tdNombre.appendChild(inputNombre);
                            const tdMontant = document.createElement('td'); tdMontant.className = 'px-4 py-2';
                            const inputMontant = document.createElement('input');
                            inputMontant.type = 'number'; inputMontant.step = '0.01'; inputMontant.name = `data[${row.montantName}]`; inputMontant.className = 'w-full border rounded p-1 bg-gray-50';
                            tdMontant.appendChild(inputMontant);
                            tr.appendChild(tdType); tr.appendChild(tdNombre); tr.appendChild(tdMontant); tbody.appendChild(tr);
                            // auto-calc
                            const recalc = () => { const n = parseFloat(inputNombre.value||'0'); const u = parseFloat(row.unit||'0'); if (!isNaN(n) && !isNaN(u)) inputMontant.value = (n*u).toFixed(2); else inputMontant.value = ''; recomputeTotals(); };
                            inputNombre.addEventListener('input', recalc);
                        });
                        dynamicFieldsContainer.appendChild(table);
                    }

                    // Table 2: Autres lignes selon catégorie
                    if (otherRows.length) {
                        const h3 = document.createElement('h5');
                        h3.className = 'text-sm font-semibold text-gray-700 mt-6';
                        h3.textContent = 'Autres lignes';
                        dynamicFieldsContainer.appendChild(h3);
                        const { table, tbody } = buildTable(['Type', 'Nombre', 'Montant']);
                        otherRows.forEach(row => {
                            const tr = document.createElement('tr');
                            const tdType = document.createElement('td'); tdType.className = 'px-4 py-2'; tdType.textContent = row.label;
                            const tdNombre = document.createElement('td'); tdNombre.className = 'px-4 py-2';
                            const inputNombre = document.createElement('input');
                            inputNombre.type = 'number'; inputNombre.min = '0'; inputNombre.step = '1';
                            inputNombre.name = `data[${row.nombreName}]`; inputNombre.className = 'w-full border rounded p-1';
                            tdNombre.appendChild(inputNombre);
                            const tdMontant = document.createElement('td'); tdMontant.className = 'px-4 py-2';
                            const inputMontant = document.createElement('input');
                            inputMontant.type = 'number'; inputMontant.step = '0.01'; inputMontant.name = `data[${row.montantName}]`; inputMontant.className = 'w-full border rounded p-1';
                            tdMontant.appendChild(inputMontant);
                            tr.appendChild(tdType); tr.appendChild(tdNombre); tr.appendChild(tdMontant); tbody.appendChild(tr);
                            inputNombre.addEventListener('input', () => recomputeTotals());
                            inputMontant.addEventListener('input', () => recomputeTotals());
                        });
                        dynamicFieldsContainer.appendChild(table);
                    }

                    // Montant en lettres + Total
                    const totalsDiv = document.createElement('div'); totalsDiv.className = 'grid grid-cols-1 md:grid-cols-2 gap-4';
                    const totalWrap = document.createElement('div');
                    const lblTotal = document.createElement('label'); lblTotal.className = 'block text-sm font-medium text-gray-700'; lblTotal.textContent = 'Total Numéraire';
                    const inpTotal = document.createElement('input'); inpTotal.name = 'data[total_numeraires]'; inpTotal.readOnly = true; inpTotal.className = 'mt-1 w-full border rounded p-2 bg-gray-100';
                    totalWrap.appendChild(lblTotal); totalWrap.appendChild(inpTotal);
                    const lettresWrap = document.createElement('div');
                    const lblL = document.createElement('label'); lblL.className = 'block text-sm font-medium text-gray-700'; lblL.textContent = 'Montant en lettres';
                    const inpL = document.createElement('input'); inpL.name = 'data[montant_en_lettres]'; inpL.className = 'mt-1 w-full border rounded p-2';
                    lettresWrap.appendChild(lblL); lettresWrap.appendChild(inpL);
                    totalsDiv.appendChild(totalWrap); totalsDiv.appendChild(lettresWrap); dynamicFieldsContainer.appendChild(totalsDiv);

                    function recomputeTotals() {
                        const inputs = Array.from(dynamicFieldsContainer.querySelectorAll('input[name^="data["][name$="_montant]"]'));
                        let sum = 0; inputs.forEach(i => { const v = parseFloat(i.value || ''); if (!isNaN(v)) sum += v; });
                        inpTotal.value = sum ? sum.toFixed(2) : '';
                    }
                    
                } catch (error) {
                    console.error('Erreur lors du chargement des champs:', error);
                    dynamicFieldsContainer.innerHTML = `
                        <div class="text-center text-red-500">
                            <i class="fas fa-exclamation-triangle mr-2"></i> Erreur lors du chargement des champs
                        </div>
                    `;
                }
            }
            
            categorySelect.addEventListener('change', loadDynamicFields);
            
            // Validation du formulaire
            document.getElementById('entryForm').addEventListener('submit', function(e) {
                const requiredFields = ['parish_id', 'category_id', 'week_label', 'start_date', 'end_date'];
                let hasError = false;
                
                requiredFields.forEach(field => {
                    const input = document.getElementById(field);
                    if (!input.value.trim()) {
                        input.classList.add('border-red-500');
                        hasError = true;
                    } else {
                        input.classList.remove('border-red-500');
                    }
                });
                
                if (hasError) {
                    e.preventDefault();
                    alert('Veuillez remplir tous les champs obligatoires.');
                }
            });
            
            // Initialisation
            if (categorySelect.value) {
                loadDynamicFields();
            }
        });
    </script>
    @endpush
</x-app-layout>


