<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			Détails de la fiche
		</h2>
	</x-slot>

	<div class="py-6">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
				<div class="p-6 text-gray-900 space-y-4">
					<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
						<div>
							<span class="font-semibold">Paroisse:</span>
							<span>{{ $entry->parish->name }}</span>
						</div>
						<div>
							<span class="font-semibold">Catégorie:</span>
							<span>{{ $entry->category->name }}</span>
						</div>
						<div>
							<span class="font-semibold">Semaine:</span>
							<span>{{ $entry->week_label }}</span>
						</div>
						<div>
							<span class="font-semibold">Période:</span>
							<span>{{ $entry->start_date->format('d/m/Y') }} - {{ $entry->end_date->format('d/m/Y') }}</span>
						</div>
					</div>

					<div>
						<span class="font-semibold">Données:</span>
						<pre class="bg-gray-50 p-3 rounded text-sm overflow-auto">{{ json_encode($entry->data_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
					</div>

					<div class="pt-4 flex gap-3">
						@if($entry->generated_file)
							<a href="{{ route('entries.download', $entry) }}" class="px-4 py-2 bg-green-600 text-white rounded">Télécharger</a>
						@endif
						<a href="{{ route('entries.edit', $entry) }}" class="px-4 py-2 bg-indigo-600 text-white rounded">Éditer</a>
						<a href="{{ route('entries.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded">Retour</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</x-app-layout>


