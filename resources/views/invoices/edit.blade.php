<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('invoices.show', $invoice) }}"
               class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="text-lg font-semibold text-slate-800">
                Modifier — {{ $invoice->reference }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('invoices.update', $invoice) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Infos générales --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-5">
                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wide">
                    Informations générales
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Titre *</label>
                        <input type="text" name="title"
                               value="{{ old('title', $invoice->title) }}" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500
                                      focus:border-transparent bg-slate-50 transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Client *</label>
                        <select name="client_id" required
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                                       focus:outline-none focus:ring-2 focus:ring-indigo-500
                                       focus:border-transparent bg-slate-50 transition-all">
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}"
                                        {{ old('client_id', $invoice->client_id) == $client->id ? 'selected' : '' }}>
                                    {{ $client->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">TVA (%)</label>
                        <input type="number" name="tax_rate"
                               value="{{ old('tax_rate', $invoice->tax_rate) }}"
                               min="0" max="100" step="0.1"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500
                                      focus:border-transparent bg-slate-50 transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Date d'émission *</label>
                        <input type="date" name="issue_date"
                               value="{{ old('issue_date', $invoice->issue_date->format('Y-m-d')) }}" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500
                                      focus:border-transparent bg-slate-50 transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Date d'échéance</label>
                        <input type="date" name="due_date"
                               value="{{ old('due_date', $invoice->due_date?->format('Y-m-d')) }}"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500
                                      focus:border-transparent bg-slate-50 transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes</label>
                    <textarea name="notes" rows="3"
                              class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                                     focus:outline-none focus:ring-2 focus:ring-indigo-500
                                     focus:border-transparent bg-slate-50 transition-all resize-none">{{ old('notes', $invoice->notes) }}</textarea>
                </div>
            </div>

            {{-- Lignes --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wide">
                        Lignes de facture
                    </h3>
                    <button type="button" id="add-line"
                            class="flex items-center space-x-1.5 px-3 py-1.5 bg-indigo-50
                                   text-indigo-600 text-xs font-semibold rounded-lg hover:bg-indigo-100 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Ajouter une ligne</span>
                    </button>
                </div>

                <div class="grid grid-cols-12 gap-3 mb-2 px-1">
                    <div class="col-span-6 text-xs font-semibold text-slate-400 uppercase">Description</div>
                    <div class="col-span-2 text-xs font-semibold text-slate-400 uppercase text-center">Qté</div>
                    <div class="col-span-3 text-xs font-semibold text-slate-400 uppercase text-right">Prix unitaire HT</div>
                    <div class="col-span-1"></div>
                </div>

                <div id="lines-container" class="space-y-3">
                    @foreach($invoice->items as $i => $item)
                        <div class="line-row grid grid-cols-12 gap-3 items-center">
                            <div class="col-span-6">
                                <input type="text" name="items[{{ $i }}][description]"
                                       value="{{ old("items.$i.description", $item->description) }}" required
                                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm
                                              focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50">
                            </div>
                            <div class="col-span-2">
                                <input type="number" name="items[{{ $i }}][quantity]"
                                       value="{{ old("items.$i.quantity", $item->quantity) }}" min="1" required
                                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm
                                              focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 text-center line-qty">
                            </div>
                            <div class="col-span-3">
                                <input type="number" name="items[{{ $i }}][unit_price]"
                                       value="{{ old("items.$i.unit_price", $item->unit_price) }}" min="0" step="0.01" required
                                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm
                                              focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 text-right line-price">
                            </div>
                            <div class="col-span-1 flex justify-center">
                                <button type="button" onclick="removeLine(this)"
                                        class="p-1 text-slate-300 hover:text-red-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 border-t border-slate-100 pt-4">
                    <div class="flex flex-col items-end space-y-2 text-sm">
                        <div class="flex justify-between w-64">
                            <span class="text-slate-500">Sous-total HT</span>
                            <span id="subtotal" class="font-medium text-slate-800">0,00 €</span>
                        </div>
                        <div class="flex justify-between w-64">
                            <span class="text-slate-500">TVA (<span id="tva-rate-display">{{ $invoice->tax_rate }}</span>%)</span>
                            <span id="tva-amount" class="font-medium text-slate-800">0,00 €</span>
                        </div>
                        <div class="flex justify-between w-64 border-t border-slate-200 pt-2">
                            <span class="font-semibold text-slate-800">Total TTC</span>
                            <span id="total-ttc" class="font-bold text-indigo-600 text-base">0,00 €</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('invoices.show', $invoice) }}"
                   class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700
                          font-medium text-sm rounded-xl transition-all">
                    Annuler
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white
                               font-semibold text-sm rounded-xl transition-all duration-200">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>

    <script>
        let lineIndex = {{ $invoice->items->count() }};

        document.getElementById('add-line').addEventListener('click', function () {
            const container = document.getElementById('lines-container');
            const row = document.createElement('div');
            row.className = 'line-row grid grid-cols-12 gap-3 items-center';
            row.innerHTML = `
                <div class="col-span-6">
                    <input type="text" name="items[${lineIndex}][description]" required
                           placeholder="Description"
                           class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50">
                </div>
                <div class="col-span-2">
                    <input type="number" name="items[${lineIndex}][quantity]" value="1" min="1" required
                           class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 text-center line-qty"
                           oninput="recalculate()">
                </div>
                <div class="col-span-3">
                    <input type="number" name="items[${lineIndex}][unit_price]" value="0" min="0" step="0.01" required
                           class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 text-right line-price"
                           oninput="recalculate()">
                </div>
                <div class="col-span-1 flex justify-center">
                    <button type="button" onclick="removeLine(this)"
                            class="p-1 text-slate-300 hover:text-red-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            `;
            container.appendChild(row);
            lineIndex++;
            recalculate();
        });

        function removeLine(btn) {
            const rows = document.querySelectorAll('.line-row');
            if (rows.length <= 1) return;
            btn.closest('.line-row').remove();
            recalculate();
        }

        function recalculate() {
            const qtys   = document.querySelectorAll('.line-qty');
            const prices = document.querySelectorAll('.line-price');
            const tvaRate = parseFloat(document.querySelector('[name="tax_rate"]').value) || 0;
            let subtotal = 0;
            qtys.forEach((qty, i) => {
                subtotal += (parseFloat(qty.value) || 0) * (parseFloat(prices[i]?.value) || 0);
            });
            const tva   = subtotal * (tvaRate / 100);
            const total = subtotal + tva;
            document.getElementById('subtotal').textContent   = subtotal.toLocaleString('fr-FR', {minimumFractionDigits: 2}) + ' €';
            document.getElementById('tva-amount').textContent = tva.toLocaleString('fr-FR', {minimumFractionDigits: 2}) + ' €';
            document.getElementById('total-ttc').textContent  = total.toLocaleString('fr-FR', {minimumFractionDigits: 2}) + ' €';
            document.getElementById('tva-rate-display').textContent = tvaRate;
        }

        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('line-qty') ||
                e.target.classList.contains('line-price') ||
                e.target.name === 'tax_rate') {
                recalculate();
            }
        });

        recalculate();
    </script>
</x-app-layout>