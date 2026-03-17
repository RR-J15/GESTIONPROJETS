<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-800">Créer votre compte</h2>
        <p class="text-sm text-gray-500 mt-1">
            Vous avez été invité en tant que
            <span class="font-semibold text-indigo-600">
                {{ $invitation->role === 'chef_projet' ? 'Chef de projet' : 'Membre' }}
            </span>
        </p>
    </div>

    <form method="POST" action="{{ route('invite.accept', $invitation->token) }}"
          class="space-y-4">
        @csrf

        {{-- Email pré-rempli --}}
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email"
                          class="mt-1 block w-full bg-gray-50 cursor-not-allowed"
                          :value="$invitation->email"
                          disabled />
            <p class="text-xs text-gray-400 mt-1">Email associé à votre invitation.</p>
        </div>

        {{-- Nom --}}
        <div>
            <x-input-label for="name" :value="__('Nom complet')" />
            <x-text-input id="name" name="name" type="text"
                          class="mt-1 block w-full"
                          :value="old('name')"
                          required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- Mot de passe --}}
        <div>
            <x-input-label for="password" :value="__('Mot de passe')" />
            <x-text-input id="password" name="password" type="password"
                          class="mt-1 block w-full"
                          required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Confirmation --}}
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                          class="mt-1 block w-full"
                          required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-2">
            <x-primary-button class="w-full justify-center">
                Créer mon compte
            </x-primary-button>
        </div>

        <p class="text-center text-xs text-gray-400 mt-4">
            Lien valide jusqu'au {{ $invitation->expires_at->format('d/m/Y à H:i') }}
        </p>
    </form>
</x-guest-layout>