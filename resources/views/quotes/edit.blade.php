<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Modifier — {{ $quote->reference }}</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">
            <form action="{{ route('quotes.update', $quote) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Client <span class="text-red-500">*</span>
                        </label>
                        <select name="client_id"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}"
                                    {{ old('client_id', $quote->client_id) == $client->id ? 'selected' : '' }}>
                                    {{ $client->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Titre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title"
                               value="{{ old('title', $quote->title) }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'émission</label>
                        <input type="date" name="issue_date"
                               value="{{ old('issue_date', $quote->issue_date->format('Y-m-d')) }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'expiration</label>
                        <input type="date" name="expiry_date"
                               value="{{ old('expiry_date', $quote->expiry_date->format('Y-m-d')) }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">TVA (%)</label>
                        <input type="number" name="tax_rate" step="0.01"
                               value="{{ old('tax_rate', $quote->tax_rate) }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Remise globale (%)</label>
                        <input type="number" name="discount_global" step="0.01"
                               value="{{ old('discount_global', $quote->discount_global) }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                {{-- Lignes --}}
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase">Lignes du devis</h3>
                        <button type="button" onclick="addItem()"
                                class="px-3 py-1 bg-indigo-50 text-indigo-600 text-sm rounded-lg hover:bg-indigo-100">
                            + Ajouter une ligne
                        </button>
                    </div>

                    <div id="items-container" class="space-y-3">
                        @foreach($quote->items as $i => $item)
                            <div class="item-row grid grid-cols-12 gap-2 items-start">
                                <div class="col-span-5">
                                    <input type="text" name="items[{{ $i }}][description]"
                                           value="{{ old("items.{$i}.description", $item->description) }}"
                                           placeholder="Description"
                                           class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div class="col-span-2">
                                    <input type="number" name="items[{{ $i }}][quantity]" step="0.01"
                                           value="{{ old("items.{$i}.quantity", $item->quantity) }}"
                                           class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div class="col-span-2">
                                    <input type="number" name="items[{{ $i }}][unit_price]" step="0.01"
                                           value="{{ old("items.{$i}.unit_price", $item->unit_price) }}"
                                           class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div class="col-span-2">
                                    <input type="number" name="items[{{ $i }}][discount]" step="0.01"
                                           value="{{ old("items.{$i}.discount", $item->discount) }}"
                                           class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div class="col-span-1 flex items-center justify-center pt-2">
                                    <button type="button" onclick="removeItem(this)"
                                            class="text-red-400 hover:text-red-600 text-lg font-bold">×</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="3"
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes', $quote->notes) }}</textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-2">
                    <a href="{{ route('quotes.show', $quote) }}"
                       class="px-4 py-2 text-sm text-gray-600 hover:underline">Annuler</a>
                    <button type="submit"
                            class="px-5 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let itemIndex = {{ $quote->items->count() }};

        function addItem() {
            const container = document.getElementById('items-container');
            const row = document.createElement('div');
            row.className = 'item-row grid grid-cols-12 gap-2 items-start';
            row.innerHTML = `
                <div class="col-span-5">
                    <input type="text" name="items[${itemIndex}][description]"
                           placeholder="Description"
                           class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="col-span-2">
                    <input type="number" name="items[${itemIndex}][quantity]" step="0.01"
                           value="1" placeholder="Qté"
                           class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="col-span-2">
                    <input type="number" name="items[${itemIndex}][unit_price]" step="0.01"
                           placeholder="Prix HT"
                           class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="col-span-2">
                    <input type="number" name="items[${itemIndex}][discount]" step="0.01"
                           value="0" placeholder="Remise %"
                           class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="col-span-1 flex items-center justify-center pt-2">
                    <button type="button" onclick="removeItem(this)"
                            class="text-red-400 hover:text-red-600 text-lg font-bold">×</button>
                </div>
            `;
            container.appendChild(row);
            itemIndex++;
        }

        function removeItem(btn) {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length === 1) return;
            btn.closest('.item-row').remove();
        }
    </script>
</x-app-layout>