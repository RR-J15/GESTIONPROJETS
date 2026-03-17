<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Nouveau projet</h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">
            <form action="{{ route('projects.store') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Nom --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nom du projet <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Client --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                    <select name="client_id"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Aucun client --</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}"
                                {{ old('client_id', $client_id) == $client->id ? 'selected' : '' }}>
                                {{ $client->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
                </div>

                {{-- Statut + Budget --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Statut <span class="text-red-500">*</span>
                        </label>
                        <select name="status"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="brouillon" {{ old('status') === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                            <option value="en_cours"  {{ old('status') === 'en_cours'  ? 'selected' : '' }}>En cours</option>
                            <option value="en_pause"  {{ old('status') === 'en_pause'  ? 'selected' : '' }}>En pause</option>
                            <option value="termine"   {{ old('status') === 'termine'   ? 'selected' : '' }}>Terminé</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Budget (€)</label>
                        <input type="number" name="budget" step="0.01"
                               value="{{ old('budget') }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                {{-- Dates --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date de début <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="start_date"
                               value="{{ old('start_date', date('Y-m-d')) }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('start_date') border-red-500 @enderror">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date de fin <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="end_date"
                               value="{{ old('end_date', date('Y-m-d', strtotime('+30 days'))) }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('end_date') border-red-500 @enderror">
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="flex items-center justify-end space-x-3 pt-2">
                    <a href="{{ route('projects.index') }}"
                       class="px-4 py-2 text-sm text-gray-600 hover:underline">Annuler</a>
                    <button type="submit"
                            class="px-5 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                        Créer le projet
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>