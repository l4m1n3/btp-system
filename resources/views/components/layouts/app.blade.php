<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' · ' : '' }}{{ config('btp.entreprise.nom') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans text-slate-800 antialiased">
@php
    $nav = [
        ['Pilotage', [
            ['Tableau de bord', 'dashboard', 'home', 'dashboard.view', 'dashboard'],
            ['Rapports', 'rapports.index', 'chart', 'rapports.view', 'rapports.*'],
        ]],
        ['Activité', [
            ['Marchés', 'marches.index', 'document', 'marches.view', 'marches.*'],
            ['Clients', 'clients.index', 'building', 'marches.view', 'clients.*'],
            ['Chantiers', 'chantiers.index', 'building', 'chantiers.view', 'chantiers.*'],
        ]],
        ['Finances', [
            ['Dépenses', 'depenses.index', 'cash', 'depenses.view', 'depenses.*'],
            ['Catégories & fournisseurs', 'parametres.depenses', 'tag', 'parametres.manage', 'parametres.*'],
        ]],
        ['Logistique', [
            ['Matériel & engins', 'materiels.index', 'truck', 'logistique.view', 'materiels.*'],
            ['Stock matériaux', 'stock.index', 'cube', 'logistique.view', 'stock.*'],
        ]],
        ['Ressources humaines', [
            ['Employés', 'employes.index', 'id', 'rh.view', 'employes.*'],
            ['Pointage', 'pointage.index', 'clock', 'pointage.saisir', 'pointage.*'],
            ['Congés & absences', 'conges.index', 'calendar', 'rh.view', 'conges.*'],
            ['Avances', 'avances.index', 'wallet', 'rh.view', 'avances.*'],
            ['Paie', 'paie.index', 'cash', 'paie.view', 'paie.*'],
        ]],
        ['Administration', [
            ['Utilisateurs', 'utilisateurs.index', 'shield', 'users.manage', 'utilisateurs.*'],
        ]],
    ];
@endphp
<div x-data="{ menu: false }" class="min-h-full">
    {{-- Barre latérale --}}
    <div x-show="menu" x-cloak class="fixed inset-0 z-30 bg-slate-900/40 lg:hidden" x-on:click="menu = false"></div>
    <aside :class="menu ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col bg-navy-900 transition-transform lg:translate-x-0">
        <a href="{{ route('dashboard') }}" wire:navigate class="flex h-16 items-center gap-3 border-b border-white/10 px-5">
            <span class="flex size-9 items-center justify-center rounded-lg bg-brand-600 text-sm font-black text-white">BTP</span>
            <span class="leading-tight">
                <span class="block text-sm font-semibold text-white">{{ config('btp.entreprise.nom') }}</span>
                <span class="block text-xs text-slate-400">Gestion interne</span>
            </span>
        </a>
        <nav class="flex-1 space-y-5 overflow-y-auto px-3 py-4">
            @foreach ($nav as [$groupe, $liens])
                @php $visibles = collect($liens)->filter(fn ($l) => Gate::allows($l[3])); @endphp
                @if ($visibles->isNotEmpty())
                    <div>
                        <p class="px-2 pb-1.5 text-[11px] font-semibold tracking-wider text-slate-500 uppercase">{{ $groupe }}</p>
                        @foreach ($visibles as [$libelle, $route, $icone, $droit, $motif])
                            <a href="{{ route($route) }}" wire:navigate
                               @class([
                                   'flex items-center gap-3 rounded-lg px-2 py-2 text-sm font-medium transition',
                                   'bg-white/10 text-white' => request()->routeIs($motif),
                                   'text-slate-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs($motif),
                               ])>
                                <x-icon :name="$icone" @class(['text-brand-500' => request()->routeIs($motif)]) />
                                {{ $libelle }}
                            </a>
                        @endforeach
                    </div>
                @endif
            @endforeach
        </nav>
        <div class="border-t border-white/10 p-3">
            <a href="{{ route('profil') }}" wire:navigate class="flex items-center gap-3 rounded-lg px-2 py-2 hover:bg-white/5">
                <span class="flex size-8 items-center justify-center rounded-full bg-navy-700 text-xs font-bold text-white">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 2)) }}</span>
                <span class="min-w-0 leading-tight">
                    <span class="block truncate text-sm font-medium text-white">{{ auth()->user()->name }}</span>
                    <span class="block text-xs text-slate-400">{{ auth()->user()->role?->label() }}</span>
                </span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button class="w-full rounded-lg px-2 py-1.5 text-left text-xs text-slate-400 hover:bg-white/5 hover:text-white">Se déconnecter</button>
            </form>
        </div>
    </aside>

    {{-- Contenu --}}
    <div class="lg:pl-64">
        <header class="sticky top-0 z-20 flex h-14 items-center gap-3 border-b border-slate-200 bg-white/90 px-4 backdrop-blur lg:hidden">
            <button type="button" x-on:click="menu = true" class="rounded p-1.5 text-slate-600 hover:bg-slate-100" aria-label="Menu">
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
            </button>
            <span class="text-sm font-semibold">{{ config('btp.entreprise.nom') }}</span>
        </header>
        <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>
    </div>
</div>
<x-flash />
@livewireScripts
</body>
</html>
