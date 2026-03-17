<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-slate-800">Mon profil</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">

        @if(session('status') === 'profile-updated')
            <div class="p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Profil mis à jour avec succès.</span>
            </div>
        @endif

        {{-- Infos principales --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="bg-gradient-to-r from-slate-800 to-indigo-900 h-24 relative">
                <div class="absolute -bottom-10 left-6">
                    <div class="relative group">
                        @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}"
                                 id="avatar-preview"
                                 class="w-20 h-20 rounded-2xl object-cover ring-4 ring-white shadow-lg">
                        @else
                            <div id="avatar-preview-initials"
                                 class="w-20 h-20 rounded-2xl bg-indigo-500 ring-4 ring-white shadow-lg
                                        flex items-center justify-center text-white text-2xl font-bold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="pt-14 px-6 pb-6">
                <h3 class="text-lg font-semibold text-slate-800">{{ $user->name }}</h3>
                <p class="text-sm text-slate-500">
                    {{ $user->position ?? 'Aucun poste défini' }}
                    — {{ $user->role->value === 'admin' ? 'Administrateur' : ($user->role->value === 'chef_projet' ? 'Chef de projet' : 'Membre') }}
                </p>
            </div>
        </div>

        {{-- Formulaire --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-6">
                Informations personnelles
            </h3>

            <form method="POST" action="{{ route('profile.update') }}"
                  enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PATCH')

                {{-- Avatar upload --}}
                <div class="flex items-center space-x-4 p-4 bg-slate-50 rounded-xl border border-slate-200">
                    <div class="flex-shrink-0">
                        @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}"
                                 id="avatar-thumb"
                                 class="w-14 h-14 rounded-xl object-cover">
                        @else
                            <div id="avatar-thumb-initials"
                                 class="w-14 h-14 rounded-xl bg-indigo-500 flex items-center
                                        justify-center text-white text-xl font-bold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-slate-700 mb-1">Photo de profil</p>
                        <input type="file" name="avatar" id="avatar-input" accept="image/*"
                               class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3
                                      file:rounded-lg file:border-0 file:bg-indigo-50
                                      file:text-indigo-600 file:text-xs file:font-medium
                                      hover:file:bg-indigo-100 cursor-pointer">
                        <p class="text-xs text-slate-400 mt-1">JPG, PNG, WEBP — max 2MB</p>
                        @error('avatar')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Nom + Poste --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Nom complet</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500
                                      focus:border-transparent bg-slate-50 transition-all
                                      @error('name') border-red-400 @enderror">
                        @error('name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Poste / Fonction</label>
                        <input type="text" name="position" value="{{ old('position', $user->position) }}"
                               placeholder="Ex: Développeur, Designer..."
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500
                                      focus:border-transparent bg-slate-50 transition-all">
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Adresse email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  focus:border-transparent bg-slate-50 transition-all
                                  @error('email') border-red-400 @enderror">
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit"
                            class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white
                                   font-semibold text-sm rounded-xl transition-all duration-200
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>

        {{-- Changer mot de passe --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-6">
                Changer le mot de passe
            </h3>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Mot de passe actuel</label>
                    <input type="password" name="current_password"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  focus:border-transparent bg-slate-50 transition-all
                                  @error('current_password', 'updatePassword') border-red-400 @enderror">
                    @error('current_password', 'updatePassword')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Nouveau mot de passe</label>
                        <input type="password" name="password"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500
                                      focus:border-transparent bg-slate-50 transition-all
                                      @error('password', 'updatePassword') border-red-400 @enderror">
                        @error('password', 'updatePassword')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirmer</label>
                        <input type="password" name="password_confirmation"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500
                                      focus:border-transparent bg-slate-50 transition-all">
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit"
                            class="px-6 py-2.5 bg-slate-800 hover:bg-slate-900 text-white
                                   font-semibold text-sm rounded-xl transition-all duration-200">
                        Changer le mot de passe
                    </button>
                </div>
            </form>
        </div>

        {{-- Supprimer le compte --}}
        <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-6">
            <h3 class="text-sm font-semibold text-red-500 uppercase tracking-wide mb-2">
                Zone de danger
            </h3>
            <p class="text-sm text-slate-500 mb-4">
                La suppression de votre compte est irréversible. Toutes vos données seront perdues.
            </p>
            <button onclick="document.getElementById('delete-modal').classList.remove('hidden')"
                    class="px-5 py-2 bg-red-50 hover:bg-red-100 text-red-600 font-medium
                           text-sm rounded-xl transition-all border border-red-200">
                Supprimer mon compte
            </button>
        </div>
    </div>

    {{-- Modal suppression --}}
    <div id="delete-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50
                                   flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-2">Supprimer le compte</h3>
            <p class="text-sm text-slate-500 mb-6">
                Confirmez votre mot de passe pour supprimer définitivement votre compte.
            </p>
            <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-4">
                @csrf
                @method('DELETE')
                <input type="password" name="password" placeholder="Votre mot de passe"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                              focus:outline-none focus:ring-2 focus:ring-red-500 bg-slate-50">
                @error('password', 'userDeletion')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror
                <div class="flex space-x-3">
                    <button type="button"
                            onclick="document.getElementById('delete-modal').classList.add('hidden')"
                            class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700
                                   font-medium text-sm rounded-xl transition-all">
                        Annuler
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white
                                   font-semibold text-sm rounded-xl transition-all">
                        Supprimer
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Preview avatar avant upload --}}
    <script>
        document.getElementById('avatar-input')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                // Remplacer preview dans le header
                const preview = document.getElementById('avatar-preview');
                const previewInitials = document.getElementById('avatar-preview-initials');
                const thumb = document.getElementById('avatar-thumb');
                const thumbInitials = document.getElementById('avatar-thumb-initials');

                const imgHtml = `<img src="${e.target.result}" class="w-20 h-20 rounded-2xl object-cover ring-4 ring-white shadow-lg">`;
                const thumbHtml = `<img src="${e.target.result}" class="w-14 h-14 rounded-xl object-cover">`;

                if (preview) preview.src = e.target.result;
                if (previewInitials) previewInitials.outerHTML = `<img id="avatar-preview" src="${e.target.result}" class="w-20 h-20 rounded-2xl object-cover ring-4 ring-white shadow-lg">`;
                if (thumb) thumb.src = e.target.result;
                if (thumbInitials) thumbInitials.outerHTML = `<img id="avatar-thumb" src="${e.target.result}" class="w-14 h-14 rounded-xl object-cover">`;
            };
            reader.readAsDataURL(file);
        });
    </script>
</x-app-layout>