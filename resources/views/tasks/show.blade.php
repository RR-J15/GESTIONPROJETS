<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <a href="{{ route('projects.show', $task->project) }}"
                   class="text-indigo-600 hover:underline text-sm">← {{ $task->project->name }}</a>
                <span class="text-gray-400">/</span>
                <h2 class="text-xl font-semibold text-gray-800">{{ $task->title }}</h2>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('tasks.edit', $task) }}"
                   class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                    Modifier
                </a>
                <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                      onsubmit="return confirm('Supprimer cette tâche ?')">
                    @csrf
                    @method('DELETE')
                    <button class="px-4 py-2 bg-red-50 text-red-600 text-sm rounded-lg hover:bg-red-100">
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        @if(session('success'))
            <div class="p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>
        @endif

        {{-- Infos tâche --}}
        <div class="bg-white shadow rounded-lg p-6">
            @php
                $statusColors = [
                    'a_faire'     => 'bg-gray-100 text-gray-700',
                    'en_cours'    => 'bg-blue-100 text-blue-700',
                    'en_revision' => 'bg-yellow-100 text-yellow-700',
                    'termine'     => 'bg-green-100 text-green-700',
                ];
                $priorityColors = [
                    'basse'    => 'bg-gray-100 text-gray-500',
                    'normale'  => 'bg-blue-100 text-blue-600',
                    'haute'    => 'bg-orange-100 text-orange-600',
                    'critique' => 'bg-red-100 text-red-600',
                ];
                $sc = $statusColors[$task->status->value]   ?? 'bg-gray-100 text-gray-700';
                $pc = $priorityColors[$task->priority->value] ?? 'bg-gray-100 text-gray-500';
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm mb-4">
                <div>
                    <p class="text-gray-500">Statut</p>
                    <span class="mt-1 inline-block px-2 py-1 text-xs rounded-full {{ $sc }}">
                        {{ ucfirst(str_replace('_', ' ', $task->status->value)) }}
                    </span>
                </div>
                <div>
                    <p class="text-gray-500">Priorité</p>
                    <span class="mt-1 inline-block px-2 py-1 text-xs rounded-full {{ $pc }}">
                        {{ ucfirst($task->priority->value) }}
                    </span>
                </div>
                <div>
                    <p class="text-gray-500">Assigné à</p>
                    <p class="font-medium text-gray-800 mt-1">
                        {{ $task->assignedTo?->name ?? '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-500">Échéance</p>
                    <p class="font-medium mt-1 {{ $task->isEnRetard() ? 'text-red-600' : 'text-gray-800' }}">
                        {{ $task->due_date ? $task->due_date->format('d/m/Y') : '—' }}
                        @if($task->isEnRetard())
                            <span class="text-xs">(en retard)</span>
                        @endif
                    </p>
                </div>
            </div>

            @if($task->description)
                <div class="pt-4 border-t">
                    <p class="text-sm text-gray-500 mb-1">Description</p>
                    <p class="text-sm text-gray-800">{{ $task->description }}</p>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Commentaires --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">
                        Commentaires ({{ $task->comments->count() }})
                    </h3>

                    <div class="space-y-4 mb-6">
                        @forelse($task->comments as $comment)
                            <div class="flex space-x-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center
                                            text-indigo-600 text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium text-gray-800">
                                            {{ $comment->user->name }}
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            {{ $comment->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-700 mt-1">{{ $comment->content }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400">Aucun commentaire.</p>
                        @endforelse
                    </div>

                    {{-- Formulaire commentaire --}}
                    <form action="{{ route('tasks.comments.store', $task) }}" method="POST"
                          class="border-t pt-4">
                        @csrf
                        <textarea name="content" rows="3" placeholder="Ajouter un commentaire..."
                                  class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('content') border-red-500 @enderror">{{ old('content') }}</textarea>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="flex justify-end mt-2">
                            <button type="submit"
                                    class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                                Commenter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Pièces jointes --}}
            <div class="space-y-4">
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">
                        Pièces jointes ({{ $task->attachments->count() }})
                    </h3>

                    <div class="space-y-2 mb-4">
                        @forelse($task->attachments as $attachment)
                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm text-gray-800 truncate">{{ $attachment->filename }}</p>
                                    <p class="text-xs text-gray-400">{{ $attachment->readable_size }}</p>
                                </div>
                                <a href="{{ Storage::url($attachment->path) }}" target="_blank"
                                   class="ml-2 text-indigo-600 hover:underline text-xs flex-shrink-0">
                                    Télécharger
                                </a>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400">Aucun fichier.</p>
                        @endforelse
                    </div>

                    {{-- Upload fichier --}}
                    <form action="{{ route('tasks.attachments.store', $task) }}" method="POST"
                          enctype="multipart/form-data" class="border-t pt-4">
                        @csrf
                        <input type="file" name="file"
                               class="w-full text-sm text-gray-600 file:mr-3 file:py-1 file:px-3
                                      file:rounded file:border-0 file:bg-indigo-50
                                      file:text-indigo-600 hover:file:bg-indigo-100">
                        @error('file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="flex justify-end mt-2">
                            <button type="submit"
                                    class="px-4 py-2 bg-gray-800 text-white text-sm rounded-lg hover:bg-gray-900">
                                Envoyer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>