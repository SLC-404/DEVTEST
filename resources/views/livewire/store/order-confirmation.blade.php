<div class="max-w-2xl mx-auto space-y-6">
    <div class="p-8 text-center bg-white shadow-sm rounded-2xl">
        <div class="grid mx-auto text-2xl bg-green-100 rounded-full h-14 w-14 place-items-center">✓</div>
        <h1 class="mt-4 text-2xl font-bold text-gray-900">¡Gracias por tu compra!</h1>
        <p class="mt-1 text-gray-500">Tu pago fue aprobado y tu pedido quedó registrado.</p>

        <div class="grid gap-3 p-4 mt-6 text-sm rounded-xl bg-violet-50 sm:grid-cols-3">
            <div>
                <p class="text-gray-500">Folio</p>
                <p class="font-semibold text-violet-900">{{ $order->folio }}</p>
            </div>
            <div>
                <p class="text-gray-500">Fecha</p>
                <p class="font-semibold">{{ $order->ordered_at->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <p class="text-gray-500">Referencia de pago</p>
                <p class="font-semibold">{{ $order->payment_reference }}</p>
            </div>
        </div>

        <p class="mt-4 text-xs text-gray-400">Confirmación enviada a {{ $order->email }}</p>
    </div>

    <div class="p-6 space-y-4 bg-white shadow-sm rounded-2xl">
        <h2 class="font-semibold text-gray-900">Resumen del pedido</h2>

        <div class="divide-y">
            @foreach ($order->details as $detail)
                <div wire:key="detail-{{ $detail->id }}" class="flex items-center gap-4 py-3">
                    <img src="{{ $detail->product->image_url }}" alt="{{ $detail->product->name }}" class="object-cover w-12 h-12 rounded-lg">
                    <div class="flex-1">
                        <p class="font-medium">{{ $detail->product->name }}</p>
                        <p class="text-xs text-gray-400">{{ $detail->quantity }} × ${{ number_format((float) $detail->price_mxn, 2) }} MXN</p>
                    </div>
                    <p class="font-semibold">${{ number_format((float) $detail->price_mxn * $detail->quantity, 2) }}</p>
                </div>
            @endforeach
        </div>

        <table class="w-full text-sm">
            <thead class="text-xs text-gray-400 uppercase">
                <tr><th></th><th class="text-right">MXN</th><th class="text-right">USD</th></tr>
            </thead>
            <tbody class="divide-y">
                <tr><td class="py-2 text-gray-500">Subtotal</td><td class="text-right">${{ number_format((float) $order->subtotal, 2) }}</td><td class="text-right">${{ number_format($totals['subtotal_usd'], 2) }}</td></tr>
                <tr><td class="py-2 text-gray-500">IVA (16%)</td><td class="text-right">${{ number_format((float) $order->taxes, 2) }}</td><td class="text-right">${{ number_format($totals['taxes_usd'], 2) }}</td></tr>
                <tr class="text-base font-bold text-violet-900"><td class="py-2">Total</td><td class="text-right">${{ number_format((float) $order->total, 2) }}</td><td class="text-right">${{ number_format($totals['total_usd'], 2) }}</td></tr>
            </tbody>
        </table>
    </div>

    <div class="text-center">
        <a href="{{ route('home') }}" wire:navigate
           class="inline-block px-6 py-3 font-semibold text-white rounded-xl bg-violet-700 hover:bg-violet-800">
            Seguir comprando
        </a>
    </div>
</div>
