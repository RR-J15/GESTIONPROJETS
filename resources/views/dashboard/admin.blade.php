<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Tableau de bord</h2>
                <p class="text-sm text-gray-500 mt-1">Bonjour {{ auth()->user()->name }}</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="px-3 py-1 text-xs rounded-full bg-indigo-100 text-indigo-700 font-medium">
                    Admin
                </span>
                <p class="text-sm text-gray-400">{{ now()->format('d/m/Y') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- KPIs Projets --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white shadow rounded-lg p-5">
                <p class="text-xs text-gray-500 uppercase font-medium">Projets actifs</p>
                <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $activeProjects }}</p>
                <p class="text-xs text-gray-400 mt-1">sur {{ $totalProjects }} total</p>
                @if($lateProjects > 0)
                    <p class="text-xs text-red-500 mt-1">⚠ {{ $lateProjects }} en retard</p>
                @endif
            </div>
            <div class="bg-white shadow rounded-lg p-5">
                <p class="text-xs text-gray-500 uppercase font-medium">Terminés ce mois</p>
                <p class="text-3xl font-bold text-green-600 mt-2">{{ $finishedThisMonth }}</p>
                <p class="text-xs text-gray-400 mt-1">projets livrés</p>
            </div>
            <div class="bg-white shadow rounded-lg p-5">
                <p class="text-xs text-gray-500 uppercase font-medium">Taux conversion</p>
                <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $conversionRate }}%</p>
                <p class="text-xs text-gray-400 mt-1">{{ $acceptedQuotes }}/{{ $totalQuotes }} devis</p>
            </div>
            <div class="bg-white shadow rounded-lg p-5">
                <p class="text-xs text-gray-500 uppercase font-medium">CA prévisionnel</p>
                <p class="text-3xl font-bold text-indigo-600 mt-2">
                    {{ number_format($revenue, 0, ',', ' ') }} €
                </p>
                <p class="text-xs text-gray-400 mt-1">devis acceptés</p>
                @if($pendingQuotes > 0)
                    <p class="text-xs text-blue-500 mt-1">{{ $pendingQuotes }} en attente</p>
                @endif
            </div>
        </div>

        {{-- Graphique CA Mensuel --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">
                CA mensuel — 12 derniers mois
            </h3>
            <div class="flex items-end space-x-2 h-40">
                @php $maxCa = $caByMonth->max('ca') ?: 1; @endphp
                @foreach($caByMonth as $month)
                    @php $height = ($month['ca'] / $maxCa) * 100; @endphp
                    <div class="flex-1 flex flex-col items-center group relative">
                        <div class="absolute -top-6 left-1/2 -translate-x-1/2 bg-gray-800 text-white
                                    text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100
                                    whitespace-nowrap z-10">
                            {{ number_format($month['ca'], 0, ',', ' ') }} €
                        </div>
                        <div class="w-full bg-indigo-500 rounded-t transition-all duration-300 hover:bg-indigo-600"
                             style="height: {{ max($height, 2) }}%">
                        </div>
                        <p class="text-xs text-gray-400 mt-1 text-center leading-tight">
                            {{ $month['month'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Projets récents --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase">Projets récents</h3>
                        <a href="{{ route('projects.index') }}"
                           class="text-xs text-indigo-600 hover:underline">Voir tout</a>
                    </div>
                    @forelse($recentProjects as $project)
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
                                <p class="text-xs text-gray-400">
                                    {{ $project->client?->company_name ?? 'Sans client' }}
                                    — {{ $project->tasks_count }} tâche(s)
                                </p>
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

                {{-- Devis récents --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase">Devis récents</h3>
                        <a href="{{ route('quotes.index') }}"
                           class="text-xs text-indigo-600 hover:underline">Voir tout</a>
                    </div>
                    @forelse($recentQuotes as $quote)
                        @php
                            $statusColors = [
                                'brouillon' => 'bg-gray-100 text-gray-700',
                                'envoye'    => 'bg-blue-100 text-blue-700',
                                'accepte'   => 'bg-green-100 text-green-700',
                                'refuse'    => 'bg-red-100 text-red-700',
                                'expire'    => 'bg-yellow-100 text-yellow-700',
                            ];
                            $color = $statusColors[$quote->status->value] ?? 'bg-gray-100 text-gray-700';
                        @endphp
                        <div class="flex items-center justify-between py-2 border-b last:border-0">
                            <div class="min-w-0 flex-1 mr-2">
                                <a href="{{ route('quotes.show', $quote) }}"
                                   class="text-sm font-medium text-gray-800 hover:text-indigo-600 truncate block">
                                    {{ $quote->reference }} — {{ $quote->title }}
                                </a>
                                <p class="text-xs text-gray-400">{{ $quote->client->company_name }}</p>
                            </div>
                            <div class="flex items-center space-x-3 flex-shrink-0">
                                <span class="text-sm font-medium text-gray-700">
                                    {{ number_format($quote->total_ttc, 0, ',', ' ') }} €
                                </span>
                                <span class="px-2 py-1 text-xs rounded-full {{ $color }}">
                                    {{ ucfirst($quote->status->value) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">Aucun devis.</p>
                    @endforelse
                </div>
            </div>

            {{-- Colonne droite --}}
            <div class="space-y-4">

                {{-- Notifications --}}
                @if($unreadNotifications->count() > 0)
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase">Notifications</h3>
                            <a href="{{ route('notifications.index') }}"
                               class="text-xs text-indigo-600 hover:underline">Voir tout</a>
                        </div>
                        <div class="space-y-3">
                            @foreach($unreadNotifications as $notification)
                                @php
                                    $typeIcons = [
                                        'task_assigned'        => '📋',
                                        'deadline_soon'        => '⏰',
                                        'quote_status_changed' => '📄',
                                    ];
                                    $icon = $typeIcons[$notification->data['type']] ?? '🔔';
                                @endphp
                                <div class="flex items-start space-x-2">
                                    <span>{{ $icon }}</span>
                                    <div>
                                        <p class="text-xs text-gray-700">{{ $notification->data['message'] }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Utilisateurs récents --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase">Équipe</h3>
                        <a href="{{ route('admin.users.index') }}"
                           class="text-xs text-indigo-600 hover:underline">Gérer</a>
                    </div>
                    @foreach(\App\Models\User::orderBy('created_at', 'desc')->take(5)->get() as $member)
                        <div class="flex items-center space-x-3 py-2 border-b last:border-0">
                            @if($member->avatar)
                                <img src="{{ Storage::url($member->avatar) }}"
                                     class="w-7 h-7 rounded-full object-cover">
                            @else
                                <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center
                                            justify-center text-indigo-600 text-xs font-bold">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $member->name }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ $member->role->value === 'admin' ? 'Admin' : ($member->role->value === 'chef_projet' ? 'Chef de projet' : 'Membre') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>