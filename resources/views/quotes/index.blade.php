<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Devis</h2>
            <a href="{{ route('quotes.create') }}"
               class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                + Nouveau devis
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Référence</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total TTC</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiration</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($quotes as $quote)
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
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-mono text-sm text-gray-900">{{ $quote->reference }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $quote->client->company_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-800">{{ $quote->title }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full {{ $color }}">
                                    {{ ucfirst($quote->status->value) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ number_format($quote->total_ttc, 2, ',', ' ') }} €
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $quote->expiry_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('quotes.show', $quote) }}"
                                   class="text-indigo-600 hover:underline text-sm">Voir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                                Aucun devis pour le moment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($quotes->hasPages())
                <div class="px-6 py-4 border-t">{{ $quotes->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>