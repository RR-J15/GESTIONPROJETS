<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Tableau de bord</h2>
                <p class="text-sm text-gray-500 mt-1">Bonjour {{ auth()->user()->name }} 👋</p>
            </div>
            <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700 font-medium">
                Membre
            </span>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- KPIs --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white shadow rounded-lg p-5">
                <p class="text-xs text-gray-500 uppercase font-medium">Mes tâches</p>
                <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $totalTasks }}</p>
                <p class="text-xs text-gray-400 mt-1">à traiter</p>
                @if($lateTasks > 0)
                    <p class="text-xs text-red-500 mt-1">⚠ {{ $lateTasks }} en retard</p>
                @endif
            </div>
            <div class="bg-white shadow rounded-lg p-5">
                <p class="text-xs text-gray-500 uppercase font-medium">Tâches critiques</p>
                <p class="text-3xl font-bold text-red-500 mt-2">{{ $urgentTasks }}</p>
                <p class="text-xs text-gray-400 mt-1">priorité critique</p>
            </div>
            <div class="bg-white shadow rounded-lg p-5">
                <p class="text-xs text-gray-500 uppercase font-medium">Mes projets</p>
                <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $myProjects->count() }}</p>
                <p class="text-xs text-gray-400 mt-1">dont je suis membre</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Mes tâches --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">
                        Mes tâches assignées
                    </h3>
                    @forelse($myTasks as $task)
                        @php
                            $priorityColors = [
                                'basse'    => 'text-gray-400',
                                'normale'  => 'text-blue-400',
                                'haute'    => 'text-orange-400',
                                'critique' => 'text-red-500',
                            ];
                            $statusColors = [
                                'a_faire'     => 'bg-gray-100 text-gray-600',
                                'en_cours'    => 'bg-blue-100 text-blue-600',
                                'en_revision' => 'bg-yellow-100 text-yellow-600',
                                'termine'     => 'bg-green-100 text-green-600',
                            ];
                            $pc = $priorityColors[$task->priority->value] ?? 'text-gray-400';
                            $sc = $statusColors[$task->status->value]     ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <div class="flex items-center justify-between py-3 border-b last:border-0">
                            <div class="flex items-center space-x-3">
                                <span class="{{ $pc }} text-lg">●</span>
                                <div>
                                    <a href="{{ route('tasks.show', $task) }}"
                                       class="text-sm font-medium text-gray-800 hover:text-indigo-600">
                                        {{ $task->title }}
                                    </a>
                                    <p class="text-xs text-gray-400">{{ $task->project->name }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                @if($task->due_date)
                                    <span class="text-xs {{ $task->isEnRetard() ? 'text-red-500 font-semibold' : 'text-gray-400' }}">
                                        {{ $task->due_date->format('d/m/Y') }}
                                    </span>
                                @endif
                                <span class="px-2 py-1 text-xs rounded-full {{ $sc }}">
                                    {{ ucfirst(str_replace('_', ' ', $task->status->value)) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-6">
                            Aucune tâche assignée. 🎉
                        </p>
                    @endforelse
                </div>

                {{-- Mes projets --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">Mes projets</h3>
                    @forelse($myProjects as $project)
                        @php
                            $statusColors = [
                                'brouillon' => 'bg-gray-100 text-gray-700',
                                'en_cours'  => 'bg-blue-100 text-blue-700',
                                'en_pause'  => 'bg-yellow-100 text-yellow-700',
                                'termine'   => 'bg-green-100 text-green-700',
                            ];
                            $color       = $statusColors[$project->status->value] ?? 'bg-gray-100 text-gray-700';
                            $progression = $project->progression();
                        @endphp
                        <div class="flex items-center justify-between py-3 border-b last:border-0">
                            <div class="flex-1 min-w-0 mr-4">
                                <a href="{{ route('projects.show', $project) }}"
                                   class="text-sm font-medium text-gray-800 hover:text-indigo-600 truncate block">
                                    {{ $project->name }}
                                </a>
                                <div class="flex items-center space-x-2 mt-1">
                                    {{-- Membres du projet --}}
                                    <div class="flex -space-x-1">
                                        @foreach($project->members->take(4) as $member)
                                            @if($member->avatar)
                                                <img src="{{ Storage::url($member->avatar) }}"
                                                     class="w-5 h-5 rounded-full object-cover border border-white"
                                                     title="{{ $member->name }}">
                                            @else
                                                <div class="w-5 h-5 rounded-full bg-indigo-100 border border-white
                                                            flex items-center justify-center
                                                            text-indigo-600 text-xs font-bold"
                                                     title="{{ $member->name }}">
                                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        @endforeach
                                        @if($project->members->count() > 4)
                                            <div class="w-5 h-5 rounded-full bg-gray-200 border border-white
                                                        flex items-center justify-center text-xs text-gray-500">
                                                +{{ $project->members->count() - 4 }}
                                            </div>
                                        @endif
                                    </div>
                                    <span class="text-xs text-gray-400">
                                        {{ $project->members->count() }} membre(s)
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3 flex-shrink-0">
                                <div class="flex items-center space-x-1">
                                    <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-indigo-600 h-1.5 rounded-full"
                                             style="width: {{ $progression }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-400">{{ $progression }}%</span>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full {{ $color }}">
                                    {{ ucfirst(str_replace('_', ' ', $project->status->value)) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">Aucun projet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Notifications --}}
            <div>
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase">Notifications</h3>
                        <a href="{{ route('notifications.index') }}"
                           class="text-xs text-indigo-600 hover:underline">Voir tout</a>
                    </div>
                    @forelse($unreadNotifications as $notification)
                        @php
                            $typeIcons = [
                                'task_assigned'        => '📋',
                                'deadline_soon'        => '⏰',
                                'quote_status_changed' => '📄',
                            ];
                            $icon = $typeIcons[$notification->data['type']] ?? '🔔';
                        @endphp
                        <div class="flex items-start space-x-2 py-2 border-b last:border-0">
                            <span>{{ $icon }}</span>
                            <div>
                                <p class="text-xs text-gray-700">{{ $notification->data['message'] }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                                @if(isset($notification->data['url']))
                                    <a href="{{ $notification->data['url'] }}"
                                       class="text-xs text-indigo-600 hover:underline mt-1 inline-block">
                                        Voir →
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-6">
                            Aucune notification. ✓
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
