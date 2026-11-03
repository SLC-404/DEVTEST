@php
    $statusLabels = [
        'paid'     => ['Pagado', 'bg-green-100 text-green-700'],
        'pending'  => ['Pendiente', 'bg-yellow-100 text-yellow-700'],
        'rejected' => ['Rechazado', 'bg-red-100 text-red-700'],
    ];
    $field = 'rounded-lg border-gray-300 text-sm focus:border-violet-500 focus:ring-violet-500';
@endphp

<div class="space-y-4">
    <div class="grid gap-3 p-4 bg-white shadow-sm rounded-xl md:grid-cols-4">
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Folio o correo…" class="{{ $field }}">
        <select wire:model.live="status" class="{{ $field }}">
            <option value="">Todos los estatus</option>
            @foreach ($statusLabels as $value => [$text])
                <option value="{{ $value }}">{{ $text }}</option>
            @endforeach
        </select>
        <label class="flex items-center gap-2 text-sm text-gray-500">Del
            <input type="date" wire:model.live="from" class="{{ $field }} flex-1">
        </label>
        <label class="flex items-center gap-2 text-sm text-gray-500">al
            <input type="date" wire:model.live="to" class="{{ $field }} flex-1">
        </label>
    </div>

    <div class="overflow-hidden bg-white shadow-sm rounded-xl">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-xs tracking-wide text-left text-gray-500 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">Folio</th>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Correo</th>
                        <th class="px-4 py-3 text-right">Subtotal</th>
                        <th class="px-4 py-3 text-right">IVA</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-center">Estatus</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                @forelse ($orders as $order)
                    @php [$text, $color] = $statusLabels[$order->status] ?? [$order->status, 'bg-gray-100 text-gray-700']; @endphp
                    <tbody x-data="{ open: false }" wire:key="order-{{ $order->id }}" class="border-t border-gray-100">
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $order->folio }}</td>
                            <td class="px-4 py-3">{{ $order->ordered_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">{{ $order->email }}</td>
                            <td class="px-4 py-3 text-right">${{ number_format((float) $order->subtotal, 2) }}</td>
                            <td class="px-4 py-3 text-right">${{ number_format((float) $order->taxes, 2) }}</td>
                            <td class="px-4 py-3 font-semibold text-right">${{ number_format((float) $order->total, 2) }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $color }}">{{ $text }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button @click="open = !open" class="text-sm font-medium text-violet-700 hover:text-violet-900"
                                        x-text="open ? 'Ocultar' : 'Detalle'"></button>
                            </td>
                        </tr>
                        <tr x-show="open" x-cloak class="bg-gray-50">
                            <td colspan="8" class="px-6 py-3">
                                <ul class="space-y-1 text-sm">
                                    @foreach ($order->details as $detail)
                                        <li class="flex justify-between">
                                            <span>{{ $detail->quantity }} × {{ $detail->product->name }} <span class="text-gray-400">({{ $detail->product->sku }})</span></span>
                                            <span>${{ number_format((float) $detail->price_mxn * $detail->quantity, 2) }} MXN</span>
                                        </li>
                                    @endforeach
                                </ul>
                                <p class="mt-2 text-xs text-gray-400">
                                    Tipo de cambio: ${{ number_format((float) $order->exchange_rate, 4) }} · Pago: {{ $order->payment_reference ?? '—' }}
                                </p>
                            </td>
                        </tr>
                    </tbody>
                @empty
                    <tbody>
                        <tr><td colspan="8" class="px-4 py-10 text-center text-gray-400">No hay pedidos en ese rango.</td></tr>
                    </tbody>
                @endforelse
            </table>
        </div>
        <div class="px-4 py-3 border-t">{{ $orders->links() }}</div>
    </div>
</div>
