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
<body class="font-sans antialiased bg-gray-100 text-gray-800">
    @php
        $menu = [
            ['label' => 'Dashboard',  'route' => 'admin.dashboard',        'active' => 'admin.dashboard',    'permission' => 'dashboard.show'],
            ['label' => 'Productos',  'route' => 'admin.products.index',   'active' => 'admin.products.*',   'permission' => 'products.show'],
            ['label' => 'Categorías', 'route' => 'admin.categories.index', 'active' => 'admin.categories.*', 'permission' => 'categories.show'],
            ['label' => 'Pedidos',    'route' => 'admin.orders.index',     'active' => 'admin.orders.*',     'permission' => 'orders.show'],
            ['label' => 'Reportes',   'route' => 'admin.reports.index',    'active' => 'admin.reports.*',    'permission' => 'reports.show'],
            ['label' => 'Tipo de cambio', 'route' => 'admin.exchange-rates.index', 'active' => 'admin.exchange-rates.*', 'permission' => 'exchange-rates.show'],
        ];
    @endphp

    <div x-data="{ sidebar: false }" class="min-h-screen lg:flex">
        <div x-show="sidebar" x-transition.opacity @click="sidebar = false"
             class="fixed inset-0 z-30 bg-black/40 lg:hidden"></div>

        <aside :class="sidebar ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-40 w-64 bg-violet-950 text-violet-100 transition-transform lg:static lg:translate-x-0">
            <div class="flex h-16 items-center gap-2 border-b border-white/10 px-6">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-orange-500 font-bold text-white">T</span>
                <span class="font-semibold text-white">{{ config('app.name') }}</span>
            </div>

            <nav class="space-y-1 p-4">
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
        <div class="flex min-w-0 flex-1 flex-col">
            <header class="flex h-16 items-center justify-between border-b bg-white px-4 sm:px-6">
                <div class="flex items-center gap-3">
                    <button @click="sidebar = true" class="rounded-md p-2 hover:bg-gray-100 lg:hidden" aria-label="Abrir menú">☰</button>
                    <h1 class="text-lg font-semibold">{{ $title ?? 'Panel' }}</h1>
                </div>

                <div class="flex items-center gap-4">
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
