<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — {{ $title ?? 'Dashboard' }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('image/img_test.jpeg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { font-family: 'Inter', sans-serif; }
        .sidebar-link { transition: all 0.2s ease; }
        .sidebar-link:hover { background: rgba(255,255,255,0.08); }
        .sidebar-link.active { background: rgba(99,102,241,0.3); border-left: 3px solid #6366f1; }
        .sidebar-link svg { transition: transform 0.2s ease; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

<div class="flex h-screen overflow-hidden">

    {{-- ===== SIDEBAR ===== --}}
    <aside id="sidebar"
           class="w-64 bg-slate-800 flex flex-col fixed inset-y-0 left-0 z-50
                  transform -translate-x-full lg:translate-x-0 transition-transform duration-300">

        {{-- Logo --}}
        <div class="flex items-center h-16 px-6 border-b border-slate-700">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                <img src="{{ asset('images/logo.png') }}"
                     alt="{{ config('app.name') }}"
                     class="h-8 w-auto"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                <div style="display:none"
                     class="w-8 h-8 bg-indigo-500 rounded-lg items-center justify-center">
                    <span class="text-white font-bold text-sm">C</span>
                </div>
                <span class="text-white font-semibold text-base tracking-tight">
                    {{ config('app.name') }}
                </span>
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">

            {{-- Principal --}}
            <p class="text-slate-400 text-xs font-semibold uppercase px-3 mb-2 mt-2">Principal</p>

            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}
                      flex items-center space-x-3 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="text-sm font-medium">Dashboard</span>
            </a>

            <a href="{{ route('projects.index') }}"
               class="sidebar-link {{ request()->routeIs('projects.*') ? 'active' : '' }}
                      flex items-center space-x-3 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="text-sm font-medium">Projets</span>
            </a>

            @if(auth()->user()->isAdmin())
                <a href="{{ route('quotes.index') }}"
                   class="sidebar-link {{ request()->routeIs('quotes.*') ? 'active' : '' }}
                          flex items-center space-x-3 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="text-sm font-medium">Devis</span>
                </a>

                @if(auth()->user()->isAdmin())
                <a href="{{ route('invoices.index') }}"
                   class="sidebar-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}
                          flex items-center space-x-3 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002          2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="text-sm font-medium">Factures</span>
                </a>
                @endif

                <a href="{{ route('clients.index') }}"
                   class="sidebar-link {{ request()->routeIs('clients.*') ? 'active' : '' }}
                          flex items-center space-x-3 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="text-sm font-medium">Clients</span>
                </a>
            @endif

            {{-- Notifications --}}
            <a href="{{ route('notifications.index') }}"
               class="sidebar-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}
                      flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-300 hover:text-white">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="text-sm font-medium">Notifications</span>
                </div>
                @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
                @if($unread > 0)
                    <span class="bg-indigo-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                        {{ $unread > 9 ? '9+' : $unread }}
                    </span>
                @endif
            </a>

            {{-- Admin --}}
            @if(auth()->user()->isAdmin())
                <p class="text-slate-400 text-xs font-semibold uppercase px-3 mb-2 mt-4">Administration</p>

                <a href="{{ route('admin.users.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}
                          flex items-center space-x-3 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span class="text-sm font-medium">Utilisateurs</span>
                </a>

                <a href="{{ route('admin.invitations.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.invitations*') ? 'active' : '' }}
                          flex items-center space-x-3 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-sm font-medium">Invitations</span>
                </a>
            @endif
        </nav>

        {{-- Profil utilisateur en bas --}}
        <div class="border-t border-slate-700 p-4">
            <div class="flex items-center space-x-3">
                {{-- Avatar --}}
                @if(auth()->user()->avatar)
                    <img src="{{ Storage::url(auth()->user()->avatar) }}"
                         class="w-9 h-9 rounded-full object-cover ring-2 ring-indigo-400">
                @else
                    <div class="w-9 h-9 rounded-full bg-indigo-500 flex items-center
                                justify-center text-white font-bold text-sm ring-2 ring-indigo-400">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="text-white text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                    <p class="text-slate-400 text-xs truncate">
                         {{ auth()->user()->role->value === 'admin' ? 'Administrateur' : 'Membre' }}
                    </p>
                </div>
                {{-- Menu profil --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="text-slate-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                        </svg>
                    </button>
                    <div x-show="open"
                         @click.away="open = false"
                         x-transition
                         class="absolute bottom-8 right-0 w-48 bg-white rounded-xl shadow-xl
                                border border-slate-100 py-1 z-50">
                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center space-x-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>Mon profil</span>
                        </a>
                        <div class="border-t border-slate-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="flex items-center space-x-2 px-4 py-2 text-sm text-red-600
                                           hover:bg-red-50 w-full text-left">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                <span>Déconnexion</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    {{-- ===== CONTENU PRINCIPAL ===== --}}
    <div class="flex-1 flex flex-col min-h-screen lg:ml-64">

        {{-- NAVBAR --}}
        <header class="h-16 bg-white border-b border-slate-200 flex items-center
                       justify-between px-6 sticky top-0 z-40">
            <div class="flex items-center space-x-4">
                {{-- Burger mobile --}}
                <button id="sidebar-toggle"
                        class="lg:hidden text-slate-500 hover:text-slate-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                {{-- Titre de la page --}}
                <div>
                    @isset($header)
                        {{ $header }}
                    @endisset
                </div>
            </div>

            {{-- Droite navbar --}}
            <div class="flex items-center space-x-4">

                {{-- Notifications --}}
                <a href="{{ route('notifications.index') }}"
                   class="relative text-slate-500 hover:text-indigo-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($unread > 0)
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white
                                     text-xs w-4 h-4 flex items-center justify-center rounded-full">
                            {{ $unread > 9 ? '9+' : $unread }}
                        </span>
                    @endif
                </a>

                {{-- Séparateur --}}
                <div class="w-px h-6 bg-slate-200"></div>

                {{-- Avatar + Nom --}}
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center space-x-3 hover:opacity-80 transition-opacity">
                    @if(auth()->user()->avatar)
                        <img src="{{ Storage::url(auth()->user()->avatar) }}"
                             class="w-8 h-8 rounded-full object-cover">
                    @else
                        <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center
                                    justify-center text-white font-bold text-sm">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="hidden sm:block">
                        <p class="text-sm font-medium text-slate-800 leading-tight">
                            {{ auth()->user()->name }}
                        </p>
                        <p class="text-xs text-slate-400 leading-tight">
                            {{ auth()->user()->position ?? auth()->user()->role->value }}
                        </p>
                    </div>
                </a>
            </div>
        </header>

        {{-- CONTENU --}}
        <main class="flex-1 overflow-y-auto p-6">
            {{ $slot }}
        </main>
    </div>
</div>

{{-- Overlay mobile --}}
<div id="sidebar-overlay"
     class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

<script>
    // Toggle sidebar mobile
    const toggle    = document.getElementById('sidebar-toggle');
    const sidebar   = document.getElementById('sidebar');
    const overlay   = document.getElementById('sidebar-overlay');

    toggle?.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    });

    overlay?.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    });
</script>

</body>
</html>