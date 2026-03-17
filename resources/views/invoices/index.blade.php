<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-slate-800">Factures</h2>
    </x-slot>

    <div class="space-y-6">

        {{-- Header + bouton --}}
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">
                    {{ $invoices->total() }} facture(s) au total
                </p>
            </div>
            <a href="{{ route('invoices.create') }}"
               class="flex items-center space-x-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700
                      text-white text-sm font-semibold rounded-xl transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Nouvelle facture</span>
            </a>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl flex items-center space-x-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl flex items-center space-x-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- KPIs --}}
        @php
            $total      = $invoices->total();
            $enAttente  = \App\Models\Invoice::where('status', 'en_attente')->count();
            $payees     = \App\Models\Invoice::where('status', 'payee')->count();
            $enRetard   = \App\Models\Invoice::where('status', 'en_retard')->count();
            $caTotal    = \App\Models\Invoice::where('status', 'payee')->with('items')->get()->sum(fn($i) => $i->total_ttc);
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5">
                <p class="text-xs text-slate-500 uppercase font-medium">Total factures</p>
                <p class="text-3xl font-bold text-slate-800 mt-2">{{ $total }}</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5">
                <p class="text-xs text-slate-500 uppercase font-medium">En attente</p>
                <p class="text-3xl font-bold text-amber-500 mt-2">{{ $enAttente }}</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5">
                <p class="text-xs text-slate-500 uppercase font-medium">En retard</p>
                <p class="text-3xl font-bold text-red-500 mt-2">{{ $enRetard }}</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5">
                <p class="text-xs text-slate-500 uppercase font-medium">CA encaissé</p>
                <p class="text-2xl font-bold text-emerald-600 mt-2">
                    {{ number_format($caTotal, 0, ',', ' ') }} €
                </p>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Référence</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Client</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase hidden md:table-cell">Devis lié</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase hidden lg:table-cell">Date</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase hidden lg:table-cell">Échéance</th>
                        <th class="text-right px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Montant TTC</th>
                        <th class="text-center px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Statut</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($invoices as $invoice)
                        @php
                            $statusConfig = [
                                'en_attente' => ['bg-amber-100 text-amber-700',  'En attente'],
                                'payee'      => ['bg-emerald-100 text-emerald-700', 'Payée'],
                                'en_retard'  => ['bg-red-100 text-red-700',      'En retard'],
                            ];
                            [$badge, $label] = $statusConfig[$invoice->status->value] ?? ['bg-gray-100 text-gray-700', '—'];
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('invoices.show', $invoice) }}"
                                   class="font-semibold text-indigo-600 hover:text-indigo-700">
                                    {{ $invoice->reference }}
                                </a>
                                <p class="text-xs text-slate-400 mt-0.5 truncate max-w-[140px]">{{ $invoice->title }}</p>
                            </td>
                            <td class="px-6 py-4 text-slate-700">{{ $invoice->client->company_name }}</td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                @if($invoice->quote)
                                    <a href="{{ route('quotes.show', $invoice->quote) }}"
                                       class="text-xs text-indigo-500 hover:underline">
                                        {{ $invoice->quote->reference }}
                                    </a>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500 hidden lg:table-cell">
                                {{ $invoice->issue_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell">
                                @if($invoice->due_date)
                                    <span class="{{ $invoice->isEnRetard() ? 'text-red-500 font-semibold' : 'text-slate-500' }}">
                                        {{ $invoice->due_date->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-slate-800">
                                {{ number_format($invoice->total_ttc, 2, ',', ' ') }} €
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $badge }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('invoices.pdf', $invoice) }}"
                                       class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all"
                                       title="Télécharger PDF">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </a>
                                    @if($invoice->status->value !== 'payee')
                                        <a href="{{ route('invoices.edit', $invoice) }}"
                                           class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all"
                                           title="Modifier">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('invoices.destroy', $invoice) }}"
                                              onsubmit="return confirm('Supprimer cette facture ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                                    title="Supprimer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center space-y-3">
                                    <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center">
                                        <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                    <p class="text-slate-500 font-medium">Aucune facture</p>
                                    <a href="{{ route('invoices.create') }}"
                                       class="text-sm text-indigo-600 hover:underline">
                                        Créer la première facture
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($invoices->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $invoices->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>