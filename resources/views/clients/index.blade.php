<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Clients</h2>
            <a href="{{ route('clients.create') }}"
               class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                + Nouveau client
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Message succès --}}
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        {{-- Tableau --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Société</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Téléphone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Devis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Projets</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($clients as $client)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                <a href="{{ route('clients.show', $client) }}"
                                   class="hover:text-indigo-600">
                                    {{ $client->company_name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $client->contact_name ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $client->email ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $client->phone ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $client->quotes_count }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $client->projects_count }}</td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('clients.edit', $client) }}"
                                   class="text-indigo-600 hover:underline text-sm">Modifier</a>
                                <form action="{{ route('clients.destroy', $client) }}"
                                      method="POST" class="inline"
                                      onsubmit="return confirm('Supprimer ce client ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline text-sm">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                                Aucun client pour le moment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            @if($clients->hasPages())
                <div class="px-6 py-4 border-t">
                    {{ $clients->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>