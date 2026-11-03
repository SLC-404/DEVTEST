@php $field = 'rounded-lg border-gray-300 text-sm focus:border-violet-500 focus:ring-violet-500'; @endphp

<div class="space-y-6">
    <div class="flex flex-col gap-3 p-4 bg-white shadow-sm rounded-xl md:flex-row md:items-center">
        <label class="flex items-center gap-2 text-sm text-gray-500">Del
            <input type="date" wire:model.live="from" class="{{ $field }}">
        </label>
        <label class="flex items-center gap-2 text-sm text-gray-500">al
            <input type="date" wire:model.live="to" class="{{ $field }}">
        </label>
        <select wire:model.live="groupBy" class="{{ $field }}">
            <option value="day">Por día</option>
            <option value="month">Por mes</option>
        </select>
        <span wire:loading class="text-xs text-gray-400">Calculando…</span>
    </div>

    @php $avg = $kpis->orders ? $kpis->sales / $kpis->orders : 0; @endphp
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="p-5 bg-white shadow-sm rounded-xl">
            <p class="text-sm text-gray-500">Ventas (sin IVA) MXN</p>
            <p class="mt-2 text-2xl font-bold">${{ number_format((float) $kpis->sales, 2) }}</p>
        </div>
        <div class="p-5 bg-white shadow-sm rounded-xl">
            <p class="text-sm text-gray-500">Ganancia MXN</p>
            <p class="mt-2 text-2xl font-bold text-orange-600">${{ number_format((float) $kpis->profit, 2) }}</p>
        </div>
        <div class="p-5 bg-white shadow-sm rounded-xl">
            <p class="text-sm text-gray-500">Pedidos pagados</p>
            <p class="mt-2 text-2xl font-bold">{{ $kpis->orders }}</p>
        </div>
        <div class="p-5 bg-white shadow-sm rounded-xl">
            <p class="text-sm text-gray-500">Ticket promedio MXN</p>
            <p class="mt-2 text-2xl font-bold">${{ number_format($avg, 2) }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="p-5 bg-white shadow-sm rounded-xl lg:col-span-2">
            <p class="mb-3 font-semibold text-gray-900">Ventas y ganancias (MXN)</p>
            <div wire:ignore class="h-72"
                 x-data
                 x-init="
                     window.salesChart?.destroy();
                     window.salesChart = new Chart($refs.canvas, {
                         type: 'bar',
                         data: {
                             labels: @js($chart['labels']),
                             datasets: [
                                 { label: 'Ventas', data: @js($chart['sales']), backgroundColor: '#6d28d9', borderRadius: 4 },
                                 { label: 'Ganancia', data: @js($chart['profit']), backgroundColor: '#f97316', borderRadius: 4 },
                             ],
                         },
                         options: {
                             responsive: true,
                             maintainAspectRatio: false,
                             scales: { y: { ticks: { callback: (v) => '$' + v.toLocaleString('es-MX') } } },
                         },
                     });
                 "
                 x-on:report-updated.window="
                     if (window.salesChart) {
                         window.salesChart.data.labels = $event.detail.labels;
                         window.salesChart.data.datasets[0].data = $event.detail.sales;
                         window.salesChart.data.datasets[1].data = $event.detail.profit;
                         window.salesChart.update();
                     }
                 ">
                <canvas x-ref="canvas"></canvas>
            </div>
        </div>
        <div class="p-5 bg-white shadow-sm rounded-xl">
            <p class="mb-3 font-semibold text-gray-900">Top 3 más vendidos</p>
            <ol class="space-y-3">
                @forelse ($topProducts as $index => $row)
                    <li wire:key="top-{{ $row->product_id }}" class="flex items-center gap-3">
                        <span class="grid w-8 h-8 font-bold rounded-full shrink-0 place-items-center bg-violet-100 text-violet-800">{{ $index + 1 }}</span>
                        <img src="{{ $row->product->image_url }}" alt="{{ $row->product->name }}" class="object-cover w-10 h-10 rounded-lg">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium truncate">{{ $row->product->name }}</p>
                            <p class="text-xs text-gray-400">{{ $row->units }} piezas · ${{ number_format((float) $row->sales, 2) }} MXN</p>
                        </div>
                    </li>
                @empty
                    <li class="text-sm text-gray-400">Sin ventas en ese periodo.</li>
                @endforelse
            </ol>
        </div>
    </div>
</div>
