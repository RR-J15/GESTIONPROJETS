<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-2">
            <a href="{{ route('projects.show', $project) }}"
               class="text-indigo-600 hover:underline text-sm">← {{ $project->name }}</a>
            <span class="text-gray-400">/</span>
            <h2 class="text-xl font-semibold text-gray-800">Nouvelle tâche</h2>
        </div>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">
            <form action="{{ route('projects.tasks.store', $project) }}" method="POST" class="space-y-5">
                @csrf

                {{-- Titre --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Titre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="4"
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
                </div>

                {{-- Assigné à + Échéance --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assigné à</label>
                        <select name="assigned_to"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Non assigné --</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}"
                                    {{ old('assigned_to') == $member->id ? 'selected' : '' }}>
                                    {{ $member->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'échéance</label>
                        <input type="date" name="due_date" value="{{ old('due_date') }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                {{-- Statut + Priorité --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select name="status"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="a_faire"     {{ old('status') === 'a_faire'     ? 'selected' : '' }}>À faire</option>
                            <option value="en_cours"    {{ old('status') === 'en_cours'    ? 'selected' : '' }}>En cours</option>
                            <option value="en_revision" {{ old('status') === 'en_revision' ? 'selected' : '' }}>En révision</option>
                            <option value="termine"     {{ old('status') === 'termine'     ? 'selected' : '' }}>Terminé</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Priorité</label>
                        <select name="priority"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="basse"    {{ old('priority') === 'basse'    ? 'selected' : '' }}>Basse</option>
                            <option value="normale"  {{ old('priority', 'normale') === 'normale'  ? 'selected' : '' }}>Normale</option>
                            <option value="haute"    {{ old('priority') === 'haute'    ? 'selected' : '' }}>Haute</option>
                            <option value="critique" {{ old('priority') === 'critique' ? 'selected' : '' }}>Critique</option>
                        </select>
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="flex items-center justify-end space-x-3 pt-2">
                    <a href="{{ route('projects.show', $project) }}"
                       class="px-4 py-2 text-sm text-gray-600 hover:underline">Annuler</a>
                    <button type="submit"
                            class="px-5 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                        Créer la tâche
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>