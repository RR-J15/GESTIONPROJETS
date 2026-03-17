<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-2">
            <a href="{{ route('tasks.show', $task) }}"
               class="text-indigo-600 hover:underline text-sm">← {{ $task->title }}</a>
            <span class="text-gray-400">/</span>
            <h2 class="text-xl font-semibold text-gray-800">Modifier la tâche</h2>
        </div>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">
            <form action="{{ route('tasks.update', $task) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Titre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title"
                           value="{{ old('title', $task->title) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="4"
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $task->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assigné à</label>
                        <select name="assigned_to"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Non assigné --</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}"
                                    {{ old('assigned_to', $task->assigned_to) == $member->id ? 'selected' : '' }}>
                                    {{ $member->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'échéance</label>
                        <input type="date" name="due_date"
                               value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select name="status"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach(['a_faire' => 'À faire', 'en_cours' => 'En cours', 'en_revision' => 'En révision', 'termine' => 'Terminé'] as $val => $label)
                                <option value="{{ $val }}"
                                    {{ old('status', $task->status->value) === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Priorité</label>
                        <select name="priority"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach(['basse' => 'Basse', 'normale' => 'Normale', 'haute' => 'Haute', 'critique' => 'Critique'] as $val => $label)
                                <option value="{{ $val }}"
                                    {{ old('priority', $task->priority->value) === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-2">
                    <a href="{{ route('tasks.show', $task) }}"
                       class="px-4 py-2 text-sm text-gray-600 hover:underline">Annuler</a>
                    <button type="submit"
                            class="px-5 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>