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
<body class="font-sans antialiased text-gray-800 bg-gray-100">
    @php
        $menu = [
            ['label' => 'Dashboard',      'route' => 'dashboard',            'active' => 'dashboard',        'permission' => 'dashboard.show'],
            ['label' => 'Productos',      'route' => 'products.index',       'active' => 'products.*',       'permission' => 'products.show'],
            ['label' => 'Categorías',     'route' => 'categories.index',     'active' => 'categories.*',     'permission' => 'categories.show'],
            ['label' => 'Pedidos',        'route' => 'orders.index',         'active' => 'orders.*',         'permission' => 'orders.show'],
            ['label' => 'Reportes',       'route' => 'reports.index',        'active' => 'reports.*',        'permission' => 'reports.show'],
            ['label' => 'Tipo de cambio', 'route' => 'exchange-rates.index', 'active' => 'exchange-rates.*', 'permission' => 'exchange-rates.show'],
        ];
    @endphp

    <div x-data="{ sidebar: false }" class="min-h-screen lg:flex">
        <div x-show="sidebar" x-transition.opacity @click="sidebar = false"
             class="fixed inset-0 z-30 bg-black/40 lg:hidden"></div>

        <aside :class="sidebar ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-40 w-64 transition-transform bg-violet-950 text-violet-100 lg:static lg:translate-x-0">
            <div class="flex items-center h-16 gap-2 px-6 border-b border-white/10">
                <span class="grid w-8 h-8 font-bold text-white bg-orange-500 rounded-lg place-items-center">T</span>
                <span class="font-semibold text-white">{{ config('app.name') }}</span>
            </div>

            <nav class="p-4 space-y-1">
                @foreach ($menu as $item)
                    @if (Route::has($item['route']) && auth()->user()->can($item['permission']))
                        <a href="{{ route($item['route']) }}" wire:navigate
                           @class([
                               'block rounded-lg px-4 py-2 text-sm font-medium transition',
                               'bg-white/10 text-white' => request()->routeIs($item['active']),
                               'hover:bg-white/5'       => ! request()->routeIs($item['active']),
                           ])>
                            {{ $item['label'] }}
                        </a>
                    @endif
                @endforeach
            </nav>
        </aside>
        <div class="flex flex-col flex-1 min-w-0">
            <header class="flex items-center justify-between h-16 px-4 bg-white border-b sm:px-6">
                <div class="flex items-center gap-3">
                    <button @click="sidebar = true" class="p-2 rounded-md hover:bg-gray-100 lg:hidden" aria-label="Abrir menú">☰</button>
                    <h1 class="text-lg font-semibold">{{ $title ?? 'Panel' }}</h1>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('home') }}" wire:navigate class="text-sm text-gray-600 hover:text-gray-900">Comprar</a>
                    <span class="hidden text-sm text-gray-600 sm:block">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-sm font-medium text-violet-700 hover:text-violet-900">Cerrar sesión</button>
                    </form>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
