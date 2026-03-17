<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-slate-800">Créer un compte</h2>
        <p class="text-slate-500 text-sm mt-1">Rejoignez votre espace de travail</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nom complet</label>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                          focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                          bg-slate-50 transition-all @error('name') border-red-400 @enderror"
                   placeholder="Jean Dupont">
            @error('name')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Adresse email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                          focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                          bg-slate-50 transition-all @error('email') border-red-400 @enderror"
                   placeholder="vous@exemple.com">
            @error('email')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Mot de passe</label>
            <input type="password" name="password" required
                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                          focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                          bg-slate-50 transition-all @error('password') border-red-400 @enderror"
                   placeholder="Min. 8 caractères">
            @error('password')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirmer le mot de passe</label>
            <input type="password" name="password_confirmation" required
                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                          focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                          bg-slate-50 transition-all"
                   placeholder="••••••••">
        </div>

        <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold
                       py-2.5 px-4 rounded-xl transition-all duration-200 text-sm
                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Créer mon compte
        </button>

        <p class="text-center text-sm text-slate-500">
            Déjà un compte ?
            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700 font-medium">
                Se connecter
            </a>
        </p>
    </form>
</x-guest-layout>