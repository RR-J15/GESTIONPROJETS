<x-guest-layout>
    <div class="text-center space-y-4">
        <div class="text-5xl">⛔</div>
        <h2 class="text-2xl font-bold text-gray-800">Lien invalide</h2>
        <p class="text-sm text-gray-500">
            Ce lien d'invitation est expiré ou a déjà été utilisé.
        </p>
        <p class="text-sm text-gray-500">
            Contactez votre administrateur pour obtenir un nouveau lien.
        </p>
        <a href="{{ route('login') }}"
           class="inline-block mt-4 px-5 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
            Retour à la connexion
        </a>
    </div>
</x-guest-layout>