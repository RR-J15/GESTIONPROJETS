<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('image/img_test.jpeg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>* { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 antialiased">

<div class="min-h-screen flex">

    {{-- Panneau gauche décoratif --}}
    <div class="hidden lg:flex lg:w-1/2 bg-slate-800 flex-col justify-between p-12 relative overflow-hidden">

        {{-- Cercles décoratifs --}}
        <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-600 rounded-full
                    opacity-10 -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-indigo-400 rounded-full
                    opacity-10 translate-y-1/2 -translate-x-1/2"></div>

        {{-- Logo --}}
        <div class="relative z-10">
            <img src="{{ asset('images/logo.png') }}"
                 alt="{{ config('app.name') }}"
                 class="h-10 w-auto"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
            <div style="display:none"
                 class="w-10 h-10 bg-indigo-500 rounded-xl items-center justify-center">
                <span class="text-white font-bold">C</span>
            </div>
        </div>

        {{-- Texte central --}}
        <div class="relative z-10 space-y-6">
            <h1 class="text-4xl font-bold text-white leading-tight">
                Gérez vos projets<br>
                <span class="text-indigo-400">avec précision.</span>
            </h1>
            <p class="text-slate-400 text-lg leading-relaxed">
                Planifiez, suivez et livrez vos projets et devis depuis une seule plateforme.
            </p>

            {{-- Features --}}
            <div class="space-y-3 pt-4">
                @foreach(['Gestion de projets & tâches', 'Devis professionnels en PDF', 'Collaboration en équipe', 'Tableau de bord en temps réel'] as $feature)
                    <div class="flex items-center space-x-3">
                        <div class="w-5 h-5 rounded-full bg-indigo-500 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-slate-300 text-sm">{{ $feature }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Footer --}}
        <p class="text-slate-500 text-xs relative z-10">
            © {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.
        </p>
    </div>

    {{-- Panneau droit — formulaire --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
        <div class="w-full max-w-md">
            {{-- Logo mobile --}}
            <div class="lg:hidden flex justify-center mb-8">
                <img src="{{ asset('images/logo.png') }}"
                     alt="{{ config('app.name') }}"
                     class="h-10 w-auto">
            </div>

            {{ $slot }}
        </div>
    </div>
</div>

</body>
</html>