<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Projets</h2>
            <a href={{route('projects.create')}}
               class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                + Nouveau projet
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">{{ session('error') }}</div>
        @endif

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Projet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progression</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Membres</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Échéance</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($projects as $project)
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
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <a href="{{ route('projects.show', $project) }}"
                                   class="font-medium text-gray-900 hover:text-indigo-600">
                                    {{ $project->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $project->client?->company_name ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full {{ $color }}">
                                    {{ ucfirst(str_replace('_', ' ', $project->status->value)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-24 bg-gray-200 rounded-full h-2">
                                        <div class="bg-indigo-600 h-2 rounded-full"
                                             style="width: {{ $progression }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $progression }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $project->members->count() }} membre(s)
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $project->end_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('projects.show', $project) }}"
                                   class="text-indigo-600 hover:underline text-sm">Voir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                                Aucun projet pour le moment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($projects->hasPages())
                <div class="px-6 py-4 border-t">{{ $projects->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>