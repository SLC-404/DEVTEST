<div class="space-y-6">
    <div>
        <h2 class="text-xl font-semibold">Bienvenido, {{ auth()->user()->name }}</h2>
        <p class="text-sm text-gray-500">Resumen de los últimos 30 días</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Ventas pagadas</p>
            <p class="mt-2 text-2xl font-bold">
                ${{ number_format($sales, 2) }}
                <span class="text-sm font-medium text-gray-400">MXN</span>
            </p>
        </div>

        <div class="rounded-xl bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Pedidos pagados</p>
            <p class="mt-2 text-2xl font-bold">{{ $ordersCount }}</p>
        </div>

        <div class="rounded-xl bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Productos activos</p>
            <p class="mt-2 text-2xl font-bold">{{ $activeProducts }}</p>
            @if ($lowStock > 0)
                <p class="mt-1 text-xs font-medium text-orange-600">{{ $lowStock }} con stock bajo (≤ 5)</p>
            @endif
        </div>

        <div class="rounded-xl bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Tipo de cambio USD/MXN</p>
            @if ($exchangeRate)
                <p class="mt-2 text-2xl font-bold">${{ number_format((float) $exchangeRate->rate, 4) }}</p>
                <p class="mt-1 text-xs text-gray-400">
                    {{ $exchangeRate->date->format('d/m/Y') }} · {{ ucfirst($exchangeRate->source) }}
                </p>
            @else
                <p class="mt-2 text-2xl font-bold text-gray-300">—</p>
            @endif
        </div>
    </div>
</div>
