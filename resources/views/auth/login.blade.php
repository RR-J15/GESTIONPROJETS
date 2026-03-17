<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-slate-800">Connexion</h2>
        <p class="text-slate-500 text-sm mt-1">Accédez à votre espace de travail</p>
    </div>

    @if(session('status'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Adresse email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                          focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                          bg-slate-50 transition-all @error('email') border-red-400 @enderror"
                   placeholder="vous@exemple.com">
            @error('email')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label class="block text-sm font-medium text-slate-700">Mot de passe</label>
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-xs text-indigo-600 hover:text-indigo-700">
                        Mot de passe oublié ?
                    </a>
                @endif
            </div>
            <input type="password" name="password" required
                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                          focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                          bg-slate-50 transition-all @error('password') border-red-400 @enderror"
                   placeholder="••••••••">
            @error('password')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center">
            <input type="checkbox" name="remember" id="remember"
                   class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
            <label for="remember" class="ml-2 text-sm text-slate-600">Se souvenir de moi</label>
        </div>

        <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold
                       py-2.5 px-4 rounded-xl transition-all duration-200 text-sm
                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Se connecter
        </button>
    </form>
</x-guest-layout>