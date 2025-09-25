<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			Détails de la paroisse
		</h2>
	</x-slot>

	<div class="py-6">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
				<div class="p-6 text-gray-900 space-y-4">
					<div>
						<span class="font-semibold">Nom:</span>
						<span>{{ $parish->name }}</span>
					</div>
					<div>
						<span class="font-semibold">Suivi des paiements:</span>
						<span>{{ $parish->enable_payment_tracking ? 'Activé' : 'Désactivé' }}</span>
					</div>
					<div>
						<span class="font-semibold">Montant hebdomadaire:</span>
						<span>{{ number_format($parish->weekly_payment_amount, 2, ',', ' ') }} €</span>
					</div>
					<div>
						<span class="font-semibold">Chemin de stockage:</span>
						<span>{{ $parish->storage_path }}</span>
					</div>

					<div class="pt-4 flex gap-3">
						<a href="{{ route('parishes.edit', $parish) }}" class="px-4 py-2 bg-indigo-600 text-white rounded">Éditer</a>
						<a href="{{ route('parishes.categories.index', $parish) }}" class="px-4 py-2 bg-blue-600 text-white rounded">Voir les catégories</a>
						<a href="{{ route('parishes.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded">Retour</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</x-app-layout>


