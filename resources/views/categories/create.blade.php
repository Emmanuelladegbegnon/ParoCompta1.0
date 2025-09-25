<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nouvelle catégorie - {{ $parish->name }}</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('parishes.categories.store', $parish) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium">Nom</label>
                        <input name="name" class="mt-1 w-full border rounded p-2" required />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Modèle Word (.docx) — optionnel</label>
                            <input type="file" name="template_file" accept=".docx" class="mt-1 w-full border rounded p-2" />
                            <p class="text-sm text-gray-500">Laissez vide si vous utilisez un formulaire prédéfini.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Formulaire prédéfini</label>
                            <select name="preset" class="mt-1 w-full border rounded p-2">
                                <option value="">Aucun</option>
                                <option value="autres_recettes">Autres Recettes</option>
                                <option value="quetes_paroisse">Quêtes Paroisse</option>
                                <option value="quetes_stations">Quêtes Stations</option>
                                <option value="autres_quetes">Autres Quêtes</option>
                            </select>
                            <p class="text-sm text-gray-500">Choisissez un modèle de formulaire sans Word.</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Champs (JSON)</label>
                        <textarea name="fields_json" class="mt-1 w-full border rounded p-2" rows="4" placeholder='[{"name":"billets_1000","label":"Billets 1000","type":"number"}]'></textarea>
                        <p class="text-sm text-gray-500">Définissez les champs sous forme de tableau JSON d'objets: name, label, type.</p>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('parishes.categories.index', $parish) }}" class="px-4 py-2 border rounded">Annuler</a>
                        <button class="px-4 py-2 bg-blue-600 text-white rounded">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>


