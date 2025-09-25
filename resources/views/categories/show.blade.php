<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			Détails de la catégorie
		</h2>
	</x-slot>

	<div class="py-6">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
				<div class="p-6 text-gray-900 space-y-4">
					<div>
						<span class="font-semibold">Paroisse:</span>
						<span>{{ $parish->name }}</span>
					</div>
					<div>
						<span class="font-semibold">Nom:</span>
						<span>{{ $category->name }}</span>
					</div>
					<div>
						<span class="font-semibold">Modèle:</span>
						<span>{{ $category->template_file }}</span>
					</div>
					<div>
						<span class="font-semibold">Champs:</span>
						<pre class="bg-gray-50 p-3 rounded text-sm overflow-auto">{{ json_encode($category->fields_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
					</div>

					<div class="pt-4 flex gap-3">
						<a href="{{ route('parishes.categories.edit', [$parish, $category]) }}" class="px-4 py-2 bg-indigo-600 text-white rounded">Éditer</a>
						<a href="{{ route('parishes.categories.index', $parish) }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded">Retour</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</x-app-layout>


