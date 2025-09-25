<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Modifier fiche</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('entries.update', $entry) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-sm font-medium">Paroisse</label>
                        <select name="parish_id" class="mt-1 w-full border rounded p-2" required>
                            @foreach ($parishes as $parish)
                                <option value="{{ $parish->id }}" {{ $parish->id == $entry->parish_id ? 'selected' : '' }}>{{ $parish->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Catégorie</label>
                        <select name="category_id" class="mt-1 w-full border rounded p-2" required>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ $category->id == $entry->category_id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Libellé semaine</label>
                            <input name="week_label" class="mt-1 w-full border rounded p-2" value="{{ $entry->week_label }}" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Début</label>
                            <input type="date" name="start_date" class="mt-1 w-full border rounded p-2" value="{{ $entry->start_date->toDateString() }}" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Fin</label>
                            <input type="date" name="end_date" class="mt-1 w-full border rounded p-2" value="{{ $entry->end_date->toDateString() }}" required />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Champs</label>
                        <div id="dynamic-fields" class="grid grid-cols-2 gap-4 mt-2"></div>
                        <script>
                        document.addEventListener('DOMContentLoaded', async () => {
                            const container = document.getElementById('dynamic-fields');
                            const categoryId = '{{ $entry->category_id }}';
                            const existing = @json($entry->data_json ?? []);
                            try {
                                const res = await fetch(`{{ url('categories') }}/${categoryId}/fields`);
                                const fields = await res.json();
                                (fields || []).forEach(f => {
                                    const wrap = document.createElement('div');
                                    const label = document.createElement('label');
                                    label.textContent = f.label || f.name;
                                    const input = document.createElement('input');
                                    input.name = `data[${f.name}]`;
                                    input.className = 'mt-1 w-full border rounded p-2';
                                    input.type = f.type || 'text';
                                    if (existing && existing[f.name] !== undefined) input.value = existing[f.name];
                                    wrap.appendChild(label);
                                    wrap.appendChild(input);
                                    container.appendChild(wrap);
                                });
                            } catch(e) { console.error(e); }
                        });
                        </script>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('entries.index') }}" class="px-4 py-2 border rounded">Annuler</a>
                        <button class="px-4 py-2 bg-blue-600 text-white rounded">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>


