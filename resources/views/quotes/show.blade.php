<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('quotes.index') }}"
                   class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">{{ $quote->reference }}</h2>
                    <p class="text-xs text-slate-400">{{ $quote->title }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                {{-- Modifier si pas accepté/refusé --}}
                @if(!in_array($quote->status->value, ['accepte', 'refuse']))
                    <a href="{{ route('quotes.edit', $quote) }}"
                       class="flex items-center space-x-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700
                              text-white text-sm font-semibold rounded-xl transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Modifier</span>
                    </a>
                @endif

                {{-- Convertir en projet si accepté --}}
                @if($quote->status->value === 'accepte' && !$quote->project)
                    <form action="{{ route('quotes.convert', $quote) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="flex items-center space-x-2 px-4 py-2 bg-emerald-600
                                       hover:bg-emerald-700 text-white text-sm font-semibold
                                       rounded-xl transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span>Convertir en projet</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">

        @if(session('success'))
            <div class="p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Infos devis --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-xs text-slate-400 uppercase font-medium mb-1">Client</p>
                    <p class="font-semibold text-slate-800">{{ $quote->client->company_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase font-medium mb-1">Statut</p>
                    @php
                        $statusColors = [
                            'brouillon' => 'bg-gray-100 text-gray-700',
                            'envoye'    => 'bg-blue-100 text-blue-700',
                            'accepte'   => 'bg-emerald-100 text-emerald-700',
                            'refuse'    => 'bg-red-100 text-red-700',
                            'expire'    => 'bg-amber-100 text-amber-700',
                        ];
                        $color = $statusColors[$quote->status->value] ?? 'bg-gray-100 text-gray-700';
                    @endphp
                    <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $color }}">
                        {{ ucfirst($quote->status->value) }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase font-medium mb-1">Émis le</p>
                    <p class="font-medium text-slate-700">{{ $quote->issue_date->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase font-medium mb-1">Expire le</p>
                    <p class="font-medium text-slate-700">{{ $quote->expiry_date->format('d/m/Y') }}</p>
                </div>
            </div>

            {{-- Changement de statut --}}
            @if(!in_array($quote->status->value, ['accepte', 'refuse']))
                <div class="mt-5 pt-5 border-t border-slate-100">
                    <form action="{{ route('quotes.status', $quote) }}" method="POST"
                          class="flex items-center space-x-3">
                        @csrf
                        @method('PATCH')
                        <label class="text-sm text-slate-500">Changer le statut :</label>
                        <select name="status"
                                class="px-3 py-2 border border-slate-200 rounded-xl text-sm
                                       focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50">
                            @foreach(['brouillon','envoye','accepte','refuse','expire'] as $s)
                                <option value="{{ $s }}" {{ $quote->status->value === $s ? 'selected' : '' }}>
                                    {{ ucfirst($s) }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit"
                                class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white
                                       text-sm font-medium rounded-xl transition-all">
                            Appliquer
                        </button>
                    </form>
                </div>
            @endif
        </div>

        {{-- Tableau des lignes --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Description</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Qté</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Prix HT</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Remise</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Total HT</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($quote->items as $item)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-3 text-slate-700">{{ $item->description }}</td>
                            <td class="px-4 py-3 text-right text-slate-600">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-right text-slate-600">
                                {{ number_format($item->unit_price, 2, ',', ' ') }} €
                            </td>
                            <td class="px-4 py-3 text-right text-slate-600">{{ $item->discount }} %</td>
                            <td class="px-6 py-3 text-right font-semibold text-slate-800">
                                {{ number_format($item->total_ht, 2, ',', ' ') }} €
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Totaux --}}
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                <div class="flex flex-col items-end space-y-2 text-sm">
                    <div class="flex justify-between w-64">
                        <span class="text-slate-500">Sous-total HT</span>
                        <span class="font-medium text-slate-700">{{ number_format($quote->subtotal, 2, ',', ' ') }} €</span>
                    </div>
                    @if($quote->discount_global > 0)
                        <div class="flex justify-between w-64">
                            <span class="text-slate-500">Remise globale ({{ $quote->discount_global }}%)</span>
                            <span class="text-red-500 font-medium">
                                - {{ number_format($quote->subtotal - $quote->total_ht, 2, ',', ' ') }} €
                            </span>
                        </div>
                    @endif
                    <div class="flex justify-between w-64">
                        <span class="text-slate-500">Total HT</span>
                        <span class="font-medium text-slate-700">{{ number_format($quote->total_ht, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="flex justify-between w-64">
                        <span class="text-slate-500">TVA ({{ $quote->tax_rate }}%)</span>
                        <span class="font-medium text-slate-700">{{ number_format($quote->tva_amount, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="flex justify-between w-64 border-t border-slate-200 pt-2">
                        <span class="font-bold text-slate-800">Total TTC</span>
                        <span class="font-bold text-indigo-600 text-base">
                            {{ number_format($quote->total_ttc, 2, ',', ' ') }} €
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        @if($quote->notes)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-2">Notes</h3>
                <p class="text-sm text-slate-600">{{ $quote->notes }}</p>
            </div>
        @endif

        {{-- Projet lié --}}
        @if($quote->project)
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-sm flex items-center space-x-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="text-emerald-700 font-medium">Projet lié :</span>
                <a href="{{ route('projects.show', $quote->project) }}"
                   class="text-emerald-800 hover:underline font-semibold">
                    {{ $quote->project->name }}
                </a>
            </div>
        @endif

        {{-- BOUTON PDF — bas droite --}}
        <div class="flex justify-end pb-4">
            <a href="{{ route('quotes.pdf', $quote) }}"
               class="flex items-center space-x-2 px-5 py-2.5 bg-slate-800 hover:bg-slate-900
                      text-white text-sm font-semibold rounded-xl transition-all duration-200
                      shadow-sm hover:shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Télécharger le devis PDF</span>
            </a>
        </div>

    </div>
</x-app-layout>