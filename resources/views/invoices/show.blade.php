<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('invoices.index') }}"
                   class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">{{ $invoice->reference }}</h2>
                    <p class="text-xs text-slate-400">{{ $invoice->title }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('invoices.pdf', $invoice) }}"
                   class="flex items-center space-x-2 px-4 py-2 bg-slate-100 hover:bg-slate-200
                          text-slate-700 text-sm font-medium rounded-xl transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>PDF</span>
                </a>
                @if($invoice->status->value !== 'payee')
                    <a href="{{ route('invoices.edit', $invoice) }}"
                       class="flex items-center space-x-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700
                              text-white text-sm font-semibold rounded-xl transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Modifier</span>
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">

        @if(session('success'))
            <div class="p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Statut + actions --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center space-x-4">
                    @php
                        $statusConfig = [
                            'en_attente' => ['bg-amber-100 text-amber-700 border-amber-200',  '⏳ En attente'],
                            'payee'      => ['bg-emerald-100 text-emerald-700 border-emerald-200', '✅ Payée'],
                            'en_retard'  => ['bg-red-100 text-red-700 border-red-200',        '🔴 En retard'],
                        ];
                        [$badge, $label] = $statusConfig[$invoice->status->value];
                    @endphp
                    <span class="px-4 py-1.5 text-sm font-semibold rounded-full border {{ $badge }}">
                        {{ $label }}
                    </span>
                    @if($invoice->due_date)
                        <span class="text-sm {{ $invoice->isEnRetard() ? 'text-red-500 font-semibold' : 'text-slate-500' }}">
                            Échéance : {{ $invoice->due_date->format('d/m/Y') }}
                        </span>
                    @endif
                </div>

                {{-- Changer le statut --}}
                @if($invoice->status->value !== 'payee')
                    <form method="POST" action="{{ route('invoices.status', $invoice) }}"
                          class="flex items-center space-x-2">
                        @csrf @method('PATCH')
                        <select name="status"
                                class="px-3 py-2 border border-slate-200 rounded-xl text-sm
                                       focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50">
                            <option value="en_attente" {{ $invoice->status->value === 'en_attente' ? 'selected' : '' }}>
                                En attente
                            </option>
                            <option value="payee">Payée</option>
                            <option value="en_retard" {{ $invoice->status->value === 'en_retard' ? 'selected' : '' }}>
                                En retard
                            </option>
                        </select>
                        <button type="submit"
                                class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white
                                       text-sm font-medium rounded-xl transition-all">
                            Mettre à jour
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Détails facture --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Infos --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-4">
                        Détails
                    </h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-slate-400 text-xs mb-1">Référence</p>
                            <p class="font-semibold text-slate-800">{{ $invoice->reference }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-xs mb-1">Date d'émission</p>
                            <p class="font-medium text-slate-700">{{ $invoice->issue_date->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-xs mb-1">Client</p>
                            <p class="font-medium text-slate-700">{{ $invoice->client->company_name }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-xs mb-1">Créée par</p>
                            <p class="font-medium text-slate-700">{{ $invoice->creator->name }}</p>
                        </div>
                        @if($invoice->quote)
                            <div class="col-span-2">
                                <p class="text-slate-400 text-xs mb-1">Devis lié</p>
                                <a href="{{ route('quotes.show', $invoice->quote) }}"
                                   class="text-indigo-600 hover:underline font-medium">
                                    {{ $invoice->quote->reference }} — {{ $invoice->quote->title }}
                                </a>
                            </div>
                        @endif
                        @if($invoice->notes)
                            <div class="col-span-2">
                                <p class="text-slate-400 text-xs mb-1">Notes</p>
                                <p class="text-slate-600">{{ $invoice->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Lignes --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Description</th>
                                <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Qté</th>
                                <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Prix HT</th>
                                <th class="text-right px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Total HT</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($invoice->items as $item)
                                <tr>
                                    <td class="px-6 py-3 text-slate-700">{{ $item->description }}</td>
                                    <td class="px-4 py-3 text-center text-slate-600">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 text-right text-slate-600">
                                        {{ number_format($item->unit_price, 2, ',', ' ') }} €
                                    </td>
                                    <td class="px-6 py-3 text-right font-medium text-slate-800">
                                        {{ number_format($item->total, 2, ',', ' ') }} €
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Totaux --}}
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                        <div class="flex flex-col items-end space-y-2 text-sm">
                            <div class="flex justify-between w-56">
                                <span class="text-slate-500">Sous-total HT</span>
                                <span class="font-medium">{{ number_format($invoice->subtotal, 2, ',', ' ') }} €</span>
                            </div>
                            <div class="flex justify-between w-56">
                                <span class="text-slate-500">TVA ({{ $invoice->tax_rate }}%)</span>
                                <span class="font-medium">{{ number_format($invoice->tva_amount, 2, ',', ' ') }} €</span>
                            </div>
                            <div class="flex justify-between w-56 border-t border-slate-200 pt-2">
                                <span class="font-bold text-slate-800">Total TTC</span>
                                <span class="font-bold text-indigo-600 text-base">
                                    {{ number_format($invoice->total_ttc, 2, ',', ' ') }} €
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Colonne droite --}}
            <div class="space-y-4">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-4">
                        Actions
                    </h3>
                    <div class="space-y-2">
                        <a href="{{ route('invoices.pdf', $invoice) }}"
                           class="flex items-center space-x-3 w-full px-4 py-3 bg-slate-50
                                  hover:bg-indigo-50 text-slate-700 hover:text-indigo-600
                                  rounded-xl transition-all text-sm font-medium border border-slate-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Télécharger PDF</span>
                        </a>

                        @if($invoice->status->value !== 'payee')
                            <a href="{{ route('invoices.edit', $invoice) }}"
                               class="flex items-center space-x-3 w-full px-4 py-3 bg-slate-50
                                      hover:bg-amber-50 text-slate-700 hover:text-amber-600
                                      rounded-xl transition-all text-sm font-medium border border-slate-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <span>Modifier la facture</span>
                            </a>

                            <form method="POST" action="{{ route('invoices.destroy', $invoice) }}"
                                  onsubmit="return confirm('Supprimer cette facture définitivement ?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="flex items-center space-x-3 w-full px-4 py-3 bg-slate-50
                                               hover:bg-red-50 text-slate-700 hover:text-red-600
                                               rounded-xl transition-all text-sm font-medium border border-slate-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    <span>Supprimer</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>