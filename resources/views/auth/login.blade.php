<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion · {{ config('btp.entreprise.nom') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-full items-center justify-center bg-navy-900 px-4 font-sans antialiased">
    <div class="w-full max-w-sm">
        <div class="mb-8 text-center">
            <span class="inline-flex size-12 items-center justify-center rounded-xl bg-brand-600 text-base font-black text-white">BTP</span>
            <h1 class="mt-4 text-xl font-semibold text-white">{{ config('btp.entreprise.nom') }}</h1>
            <p class="text-sm text-slate-400">Système de gestion interne</p>
        </div>
        <form method="POST" action="{{ route('login.submit') }}" class="space-y-4 rounded-xl bg-white p-6 shadow-xl">
            @csrf
            <x-field label="Adresse e-mail" name="email">
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="form-input">
            </x-field>
            <x-field label="Mot de passe" name="password">
                <input id="password" name="password" type="password" required autocomplete="current-password" class="form-input">
            </x-field>
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-600"> Rester connecté
            </label>
            <x-btn type="submit" class="w-full">Se connecter</x-btn>
        </form>
    </div>
</body>
</html>
