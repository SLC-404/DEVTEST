<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' · ' : '' }}{{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased text-gray-800 bg-gray-50">
    <header class="sticky top-0 z-30 border-b bg-white/90 backdrop-blur">
        <div class="flex items-center justify-between h-16 gap-4 px-4 mx-auto max-w-7xl sm:px-6">
            <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2">
                <span class="grid w-8 h-8 font-bold text-white bg-orange-500 rounded-lg place-items-center">T</span>
                <span class="font-semibold text-violet-900">{{ config('app.name') }}</span>
            </a>

            <nav class="flex items-center gap-4 text-sm font-medium">
                <livewire:store.cart-counter />
                @auth
                    @if (auth()->user()->roles()->exists())
                        <a href="{{ route('dashboard') }}" wire:navigate class="text-violet-700 hover:text-violet-900">Panel</a>
                    @endif
                    <span class="hidden text-gray-500 sm:inline">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-gray-600 hover:text-gray-900">Salir</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" wire:navigate class="text-gray-600 hover:text-gray-900">Iniciar sesión</a>
                    <a href="{{ route('register') }}" wire:navigate
                       class="rounded-lg bg-violet-700 px-3 py-1.5 text-white hover:bg-violet-800">Crear cuenta</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="px-4 py-6 mx-auto max-w-7xl sm:px-6">
        {{ $slot }}
    </main>
</body>
</html>
