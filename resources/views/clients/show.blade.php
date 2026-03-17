<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">{{ $client->company_name }}</h2>
            <a href="{{ route('clients.edit', $client) }}"
               class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                Modifier
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Infos principales --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">Informations</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-500">Contact :</span>
                    <span class="ml-2 text-gray-800">{{ $client->contact_name ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Email :</span>
                    <span class="ml-2 text-gray-800">{{ $client->email ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Téléphone :</span>
                    <span class="ml-2 text-gray-800">{{ $client->phone ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Adresse :</span>
                    <span class="ml-2 text-gray-800">{{ $client->address ?? '—' }}</span>
                </div>
                @if($client->notes)
                    <div class="sm:col-span-2">
                        <span class="text-gray-500">Notes :</span>
                        <p class="mt-1 text-gray-800">{{ $client->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Derniers devis --}}
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-500 uppercase">Derniers devis</h3>
                <a href="{{ route('quotes.create', ['client_id' => $client->id]) }}"
                   class="text-sm text-indigo-600 hover:underline">+ Nouveau devis</a>
            </div>
            @forelse($client->quotes as $quote)
                <div class="flex items-center justify-between py-2 border-b last:border-0">
                    <a href="{{ route('quotes.show', $quote) }}"
                       class="text-sm text-indigo-600 hover:underline">
                        {{ $quote->reference }} — {{ $quote->title }}
                    </a>
                    <span class="text-xs px-2 py-1 rounded-full
                        {{ $quote->status->value === 'accepte' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $quote->status->value === 'refuse'  ? 'bg-red-100 text-red-700'   : '' }}
                        {{ $quote->status->value === 'envoye'  ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $quote->status->value === 'brouillon' ? 'bg-gray-100 text-gray-700' : '' }}
                        {{ $quote->status->value === 'expire'  ? 'bg-yellow-100 text-yellow-700' : '' }}">
                        {{ ucfirst($quote->status->value) }}
                    </span>
                </div>
            @empty
                <p class="text-sm text-gray-400">Aucun devis.</p>
            @endforelse
        </div>

        {{-- Derniers projets --}}
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-500 uppercase">Derniers projets</h3>
                <a href="{{ route('projects.create', ['client_id' => $client->id]) }}"
                   class="text-sm text-indigo-600 hover:underline">+ Nouveau projet</a>
            </div>
            @forelse($client->projects as $project)
                <div class="flex items-center justify-between py-2 border-b last:border-0">
                    <a href="{{ route('projects.show', $project) }}"
                       class="text-sm text-indigo-600 hover:underline">
                        {{ $project->name }}
                    </a>
                    <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                        {{ ucfirst(str_replace('_', ' ', $project->status->value)) }}
                    </span>
                </div>
            @empty
                <p class="text-sm text-gray-400">Aucun projet.</p>
            @endforelse
        </div>

    </div>
</x-app-layout>
