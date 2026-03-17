<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Nouveau client</h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">
            <form action="{{ route('clients.store') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Société --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nom de la société <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="company_name"
                           value="{{ old('company_name') }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('company_name') border-red-500 @enderror">
                    @error('company_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Contact --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom du contact</label>
                    <input type="text" name="contact_name"
                           value="{{ old('contact_name') }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                {{-- Email + Téléphone --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email"
                               value="{{ old('email') }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                        <input type="text" name="phone"
                               value="{{ old('phone') }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                {{-- Adresse --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                    <textarea name="address" rows="2"
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('address') }}</textarea>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="3"
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes') }}</textarea>
                </div>

                {{-- Boutons --}}
                <div class="flex items-center justify-end space-x-3 pt-2">
                    <a href="{{ route('clients.index') }}"
                       class="px-4 py-2 text-sm text-gray-600 hover:underline">Annuler</a>
                    <button type="submit"
                            class="px-5 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>