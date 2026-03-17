<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Nouveau devis</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">
            <form action="{{ route('quotes.store') }}" method="POST" id="quote-form" class="space-y-6">
                @csrf

                {{-- Infos générales --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Client <span class="text-red-500">*</span>
                        </label>
                        <select name="client_id"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('client_id') border-red-500 @enderror">
                            <option value="">-- Sélectionner un client --</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}"
                                    {{ old('client_id', $client_id) == $client->id ? 'selected' : '' }}>
                                    {{ $client->company_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Titre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title') }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('title') border-red-500 @enderror">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date d'émission <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="issue_date"
                               value="{{ old('issue_date', date('Y-m-d')) }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date d'expiration <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="expiry_date"
                               value="{{ old('expiry_date', date('Y-m-d', strtotime('+30 days'))) }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">TVA (%)</label>
                        <input type="number" name="tax_rate" step="0.01"
                               value="{{ old('tax_rate', 20) }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Remise globale (%)</label>
                        <input type="number" name="discount_global" step="0.01"
                               value="{{ old('discount_global', 0) }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                {{-- Lignes du devis --}}
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase">Lignes du devis</h3>
                        <button type="button" onclick="addItem()"
                                class="px-3 py-1 bg-indigo-50 text-indigo-600 text-sm rounded-lg hover:bg-indigo-100">
                            + Ajouter une ligne
                        </button>
                    </div>

                    <div id="items-container" class="space-y-3">
                        {{-- Ligne initiale --}}
                        <div class="item-row grid grid-cols-12 gap-2 items-start">
                            <div class="col-span-5">
                                <input type="text" name="items[0][description]"
                                       placeholder="Description"
                                       value="{{ old('items.0.description') }}"
                                       class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div class="col-span-2">
                                <input type="number" name="items[0][quantity]" step="0.01"
                                       placeholder="Qté" value="{{ old('items.0.quantity', 1) }}"
                                       class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 item-qty">
                            </div>
                            <div class="col-span-2">
                                <input type="number" name="items[0][unit_price]" step="0.01"
                                       placeholder="Prix HT" value="{{ old('items.0.unit_price') }}"
                                       class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 item-price">
                            </div>
                            <div class="col-span-2">
                                <input type="number" name="items[0][discount]" step="0.01"
                                       placeholder="Remise %" value="{{ old('items.0.discount', 0) }}"
                                       class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 item-discount">
                            </div>
                            <div class="col-span-1 flex items-center justify-center pt-2">
                                <button type="button" onclick="removeItem(this)"
                                        class="text-red-400 hover:text-red-600 text-lg font-bold">×</button>
                            </div>
                        </div>
                    </div>

                    {{-- En-têtes colonnes --}}
                    <div class="grid grid-cols-12 gap-2 mt-1 px-1">
                        <div class="col-span-5 text-xs text-gray-400">Description</div>
                        <div class="col-span-2 text-xs text-gray-400">Quantité</div>
                        <div class="col-span-2 text-xs text-gray-400">Prix unitaire HT</div>
                        <div class="col-span-2 text-xs text-gray-400">Remise %</div>
                    </div>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="3"
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes') }}</textarea>
                </div>

                {{-- Boutons --}}
                <div class="flex items-center justify-end space-x-3 pt-2">
                    <a href="{{ route('quotes.index') }}"
                       class="px-4 py-2 text-sm text-gray-600 hover:underline">Annuler</a>
                    <button type="submit"
                            class="px-5 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                        Enregistrer le devis
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let itemIndex = 1;

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
                           placeholder="Qté" value="1"
                           class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 item-qty">
                </div>
                <div class="col-span-2">
                    <input type="number" name="items[${itemIndex}][unit_price]" step="0.01"
                           placeholder="Prix HT"
                           class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 item-price">
                </div>
                <div class="col-span-2">
                    <input type="number" name="items[${itemIndex}][discount]" step="0.01"
                           placeholder="Remise %" value="0"
                           class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 item-discount">
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
            if (rows.length === 1) return; // garder au moins 1 ligne
            btn.closest('.item-row').remove();
        }
    </script>
</x-app-layout>