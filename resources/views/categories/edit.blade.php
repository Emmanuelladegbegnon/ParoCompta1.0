<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Modifier catégorie - {{ $parish->name }}</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('parishes.categories.update', [$parish, $category]) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-sm font-medium">Nom</label>
                        <input name="name" class="mt-1 w-full border rounded p-2" value="{{ $category->name }}" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Nouveau modèle (.docx) facultatif</label>
                        <input type="file" name="template_file" accept=".docx" class="mt-1 w-full border rounded p-2" />
                        <p class="text-sm text-gray-500 mt-1">Actuel: {{ $category->template_file }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Champs (JSON)</label>
                        <textarea name="fields_json" class="mt-1 w-full border rounded p-2" rows="4">{{ json_encode($category->fields_json, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</textarea>
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


