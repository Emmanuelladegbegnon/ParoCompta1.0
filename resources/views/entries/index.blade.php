<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Fiches générées</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="GET" class="flex items-end gap-2 mb-4">
                <div>
                    <label class="block text-sm">Année</label>
                    <input type="number" name="year" value="{{ $year ?? '' }}" class="border rounded p-2 w-28" />
                </div>
                <div>
                    <label class="block text-sm">Mois</label>
                    <input type="number" min="1" max="12" name="month" value="{{ $month ?? '' }}" class="border rounded p-2 w-20" />
                </div>
                <div>
                    <label class="block text-sm">Trimestre</label>
                    <input type="number" min="1" max="4" name="quarter" value="{{ $quarter ?? '' }}" class="border rounded p-2 w-20" />
                </div>
                <button class="px-4 py-2 bg-gray-200 rounded">Filtrer</button>
                <a href="{{ route('entries.index') }}" class="px-4 py-2 border rounded">Réinitialiser</a>
            </form>
            <div class="flex justify-end mb-2">
                <a href="{{ route('entries.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Nouvelle fiche</a>
            </div>
            @if (session('status'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('status') }}</div>
            @endif
            <div class="bg-white shadow sm:rounded-lg p-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left">Paroisse</th>
                            <th class="px-4 py-2 text-left">Catégorie</th>
                            <th class="px-4 py-2 text-left">Semaine</th>
                            <th class="px-4 py-2 text-left">Période</th>
                            <th class="px-4 py-2 text-left">Fichier</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($entries as $entry)
                            <tr>
                                <td class="px-4 py-2">{{ $entry->parish->name }}</td>
                                <td class="px-4 py-2">{{ $entry->category->name }}</td>
                                <td class="px-4 py-2">{{ $entry->week_label }}</td>
                                <td class="px-4 py-2">{{ $entry->start_date->format('d/m/Y') }} - {{ $entry->end_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-2">{{ $entry->generated_file ? basename($entry->generated_file) : '-' }}</td>
                                <td class="px-4 py-2 space-x-2">
                                    @if ($entry->generated_file)
                                        <a class="text-indigo-600" href="{{ route('entries.download', $entry) }}">Télécharger</a>
                                    @endif
                                    <a class="text-blue-600" href="{{ route('entries.edit', $entry) }}">Éditer</a>
                                    <form action="{{ route('entries.destroy', $entry) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600" onclick="return confirm('Supprimer ?')">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">{{ $entries->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>


