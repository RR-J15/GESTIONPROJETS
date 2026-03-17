<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">{{ $project->name }}</h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $project->client?->company_name ?? 'Sans client' }}
                    — {{ $project->start_date->format('d/m/Y') }} au {{ $project->end_date->format('d/m/Y') }}
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('projects.edit', $project) }}"
                   class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                    Modifier
                </a>
                <form action="{{ route('projects.destroy', $project) }}" method="POST"
                      onsubmit="return confirm('Supprimer ce projet ?')">
                    @csrf
                    @method('DELETE')
                    <button class="px-4 py-2 bg-red-50 text-red-600 text-sm rounded-lg hover:bg-red-100">
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        @if(session('success'))
            <div class="p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-red-100 text-red-700 rounded-lg">{{ session('error') }}</div>
        @endif

        {{-- Infos + Progression --}}
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            @php
                $statusColors = [
                    'brouillon' => 'bg-gray-100 text-gray-700',
                    'en_cours'  => 'bg-blue-100 text-blue-700',
                    'en_pause'  => 'bg-yellow-100 text-yellow-700',
                    'termine'   => 'bg-green-100 text-green-700',
                ];
                $color = $statusColors[$project->status->value] ?? 'bg-gray-100 text-gray-700';
                $progression = $project->progression();
            @endphp

            <div class="bg-white shadow rounded-lg p-4 text-center">
                <p class="text-xs text-gray-500 uppercase">Statut</p>
                <span class="mt-1 inline-block px-3 py-1 text-sm rounded-full {{ $color }}">
                    {{ ucfirst(str_replace('_', ' ', $project->status->value)) }}
                </span>
            </div>
            <div class="bg-white shadow rounded-lg p-4 text-center">
                <p class="text-xs text-gray-500 uppercase">Progression</p>
                <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $progression }}%</p>
            </div>
            <div class="bg-white shadow rounded-lg p-4 text-center">
                <p class="text-xs text-gray-500 uppercase">Tâches</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $project->tasks->count() }}</p>
            </div>
            <div class="bg-white shadow rounded-lg p-4 text-center">
                <p class="text-xs text-gray-500 uppercase">Budget</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">
                    {{ $project->budget ? number_format($project->budget, 0, ',', ' ') . ' €' : '—' }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Tâches --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase">Tâches</h3>
                        <a href="{{ route('projects.tasks.create', $project) }}"
                           class="px-3 py-1 bg-indigo-50 text-indigo-600 text-sm rounded-lg hover:bg-indigo-100">
                            + Ajouter
                        </a>
                    </div>

                    @forelse($project->tasks as $task)
                        @php
                            $taskColors = [
                                'a_faire'     => 'bg-gray-100 text-gray-600',
                                'en_cours'    => 'bg-blue-100 text-blue-600',
                                'en_revision' => 'bg-yellow-100 text-yellow-600',
                                'termine'     => 'bg-green-100 text-green-600',
                            ];
                            $priorityColors = [
                                'basse'    => 'text-gray-400',
                                'normale'  => 'text-blue-400',
                                'haute'    => 'text-orange-400',
                                'critique' => 'text-red-500',
                            ];
                            $tc = $taskColors[$task->status->value] ?? 'bg-gray-100 text-gray-600';
                            $pc = $priorityColors[$task->priority->value] ?? 'text-gray-400';
                        @endphp
                        <div class="flex items-center justify-between py-3 border-b last:border-0">
                            <div class="flex items-center space-x-3">
                                <span class="{{ $pc }} font-bold text-lg">●</span>
                                <div>
                                    <a href="{{ route('tasks.show', $task) }}"
                                       class="text-sm font-medium text-gray-800 hover:text-indigo-600">
                                        {{ $task->title }}
                                    </a>
                                    @if($task->assignedTo)
                                        <p class="text-xs text-gray-400">{{ $task->assignedTo->name }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                @if($task->due_date)
                                    <span class="text-xs {{ $task->isEnRetard() ? 'text-red-500 font-semibold' : 'text-gray-400' }}">
                                        {{ $task->due_date->format('d/m/Y') }}
                                    </span>
                                @endif
                                <span class="px-2 py-1 text-xs rounded-full {{ $tc }}">
                                    {{ ucfirst(str_replace('_', ' ', $task->status->value)) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">Aucune tâche pour ce projet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Membres --}}
            <div class="space-y-4">
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">Membres</h3>

                    @foreach($project->members as $member)
                        <div class="flex items-center justify-between py-2 border-b last:border-0">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $member->name }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ $member->pivot->role === 'chef' ? 'Chef de projet' : 'Membre' }}
                                </p>
                            </div>
                            @if($project->created_by !== $member->id)
                                <form action="{{ route('projects.members.remove', [$project, $member]) }}"
                                      method="POST"
                                      onsubmit="return confirm('Retirer ce membre ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-400 hover:text-red-600 text-xs">Retirer</button>
                                </form>
                            @endif
                        </div>
                    @endforeach

                    {{-- Ajouter un membre --}}
                    @if($availableMembers->count() > 0)
                        <form action="{{ route('projects.members.add', $project) }}"
                              method="POST" class="mt-4 space-y-2">
                            @csrf
                            <select name="user_id"
                                    class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Ajouter un membre --</option>
                                @foreach($availableMembers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <select name="role"
                                    class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="membre">Membre</option>
                                <option value="chef">Chef de projet</option>
                            </select>
                            <button type="submit"
                                    class="w-full px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                                Ajouter
                            </button>
                        </form>
                    @endif
                </div>

                {{-- Devis lié --}}
                @if($project->quote)
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Devis lié</h3>
                        <a href="{{ route('quotes.show', $project->quote) }}"
                           class="text-sm text-indigo-600 hover:underline">
                            {{ $project->quote->reference }} — {{ $project->quote->title }}
                        </a>
                        <p class="text-sm font-medium text-gray-800 mt-1">
                            {{ number_format($project->quote->total_ttc, 2, ',', ' ') }} €
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
