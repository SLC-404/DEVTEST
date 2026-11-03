@php
    $input = 'mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-violet-500 focus:ring-violet-500';
    $label = 'block text-sm font-medium text-gray-700';
    $error = 'mt-1 text-xs text-red-600';
@endphp
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Cotización y pago</h1>
        <a href="{{ route('cart') }}" wire:navigate class="text-sm font-medium text-violet-700 hover:text-violet-900">←
            Editar carrito</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-5">
        <section class="p-6 space-y-4 bg-white shadow-sm rounded-2xl lg:col-span-3">
            <h2 class="font-semibold text-gray-900">Cotización #{{ $quote->id }}</h2>
            <div class="divide-y">
                @foreach ($quote->items as $item)
                    <div wire:key="quote-item-{{ $item->id }}" class="flex items-center gap-4 py-3">
                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}"
                            class="object-cover rounded-lg h-14 w-14">
                        <div class="flex-1">
                            <p class="font-medium">{{ $item->product->name }}</p>
                            <p class="text-xs text-gray-400">{{ $item->quantity }} ×
                                ${{ number_format((float) $item->price_mxn, 2) }} MXN</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold">${{ number_format((float) $item->price_mxn * $item->quantity, 2) }}
                                MXN</p>
                            <p class="text-xs text-gray-400">
                                ${{ number_format((float) $item->price_usd * $item->quantity, 2) }} USD</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <table class="w-full text-sm">
                <thead class="text-xs text-gray-400 uppercase">
                    <tr>
                        <th></th>
                        <th class="text-right">MXN</th>
                        <th class="text-right">USD</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr>
                        <td class="py-2 text-gray-500">Subtotal</td>
                        <td class="text-right">${{ number_format($totals['subtotal_mxn'], 2) }}</td>
                        <td class="text-right">${{ number_format($totals['subtotal_usd'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 text-gray-500">IVA (16%)</td>
                        <td class="text-right">${{ number_format($totals['taxes_mxn'], 2) }}</td>
                        <td class="text-right">${{ number_format($totals['taxes_usd'], 2) }}</td>
                    </tr>
                    <tr class="text-base font-bold text-violet-900">
                        <td class="py-2">Total</td>
                        <td class="text-right">${{ number_format($totals['total_mxn'], 2) }}</td>
                        <td class="text-right">${{ number_format($totals['total_usd'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
            <p class="text-xs text-gray-400">Tipo de cambio aplicado:
                ${{ number_format((float) $quote->exchange_rate, 4) }} MXN por USD.</p>
        </section>
        <form wire:submit="pay" class="p-6 space-y-4 bg-white shadow-sm h-fit rounded-2xl lg:col-span-2">
            <h2 class="font-semibold text-gray-900">Datos de pago</h2>
            <div>
                <label class="{{ $label }}">Correo electrónico</label>
                <input type="email" wire:model.blur="email" class="{{ $input }}">
                <p class="mt-1 text-xs text-gray-400">Aquí te enviaremos la confirmación del pedido.</p>
                @error('email')
                    <p class="{{ $error }}">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="{{ $label }}">Nombre en la tarjeta</label>
                <input type="text" wire:model.blur="cardName" class="{{ $input }}">
                @error('cardName')
                    <p class="{{ $error }}">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="{{ $label }}">Número de tarjeta</label>
                <input type="text" inputmode="numeric" maxlength="16" placeholder="4242424242424242"
                    wire:model.blur="cardNumber" class="{{ $input }}">
                @error('cardNumber')
                    <p class="{{ $error }}">{{ $message }}</p>
                @enderror
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="{{ $label }}">Vencimiento</label>
                    <input type="text" inputmode="numeric" maxlength="5" placeholder="MM/AA"
                        wire:model.blur="cardExpiry" x-data
                        x-on:input="
           let digits = $el.value.replace(/\D/g, '').slice(0, 4);
           $el.value = digits.length > 2 ? digits.slice(0, 2) + '/' + digits.slice(2) : digits;
       "
                        class="{{ $input }}">
                    @error('cardExpiry')
                        <p class="{{ $error }}">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="{{ $label }}">CVV</label>
                    <input type="password" inputmode="numeric" maxlength="3" wire:model.blur="cardCvv"
                        class="{{ $input }}">
                    @error('cardCvv')
                        <p class="{{ $error }}">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            @if ($paymentError)
                <div class="px-4 py-3 text-sm text-red-700 rounded-lg bg-red-50">{{ $paymentError }}</div>
            @endif
            <div class="px-4 py-3 text-xs text-gray-500 rounded-lg bg-gray-50">
                🧪 Pago simulado: las tarjetas cualquier se aprueban.
            </div>
            <button type="submit" wire:loading.attr="disabled" wire:target="pay"
                class="w-full px-6 py-3 font-semibold text-white rounded-xl bg-violet-700 hover:bg-violet-800 disabled:opacity-50">
                <span wire:loading.remove wire:target="pay">Pagar ${{ number_format($totals['total_mxn'], 2) }}
                    MXN</span>
                <span wire:loading wire:target="pay">Procesando pago…</span>
            </button>
        </form>
    </div>
</div>
