<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">Tu carrito</h1>

    @if ($items->isEmpty())
        <div class="p-10 text-center bg-white shadow-sm rounded-2xl">
            <p class="text-gray-500">Tu carrito está vacío.</p>
            <a href="{{ route('home') }}" wire:navigate
                class="mt-4 inline-block rounded-xl bg-violet-700 px-5 py-2.5 font-semibold text-white hover:bg-violet-800">
                Ver productos
            </a>
        </div>
    @else
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-3 lg:col-span-2" wire:loading.class="opacity-60">
                @foreach ($items as $item)
                    <div wire:key="cart-{{ $item->product->id }}" class="flex gap-4 p-4 bg-white shadow-sm rounded-2xl">
                        <a href="{{ route('products.detail', $item->product) }}" wire:navigate class="shrink-0">
                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}"
                                class="object-cover w-24 h-24 rounded-xl">
                        </a>

                        <div class="flex flex-col flex-1 gap-2">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold">{{ $item->product->name }}</p>
                                    <p class="text-xs text-gray-400">SKU {{ $item->product->sku }}</p>
                                    <p class="text-sm text-gray-500">
                                        ${{ number_format((float) $item->product->price_mxn, 2) }} MXN c/u</p>
                                </div>
                                <button wire:click="remove({{ $item->product->id }})"
                                    class="text-sm text-red-600 hover:text-red-800">Quitar</button>
                            </div>

                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center border rounded-xl">
                                    <button wire:click="decrement({{ $item->product->id }}, {{ $item->quantity }})"
                                        class="px-3 py-1.5 text-lg hover:bg-gray-50" aria-label="Quitar uno">−</button>
                                    <input type="number" min="1" max="{{ $item->product->stock }}"
                                        value="{{ $item->quantity }}"
                                        wire:change="setQuantity({{ $item->product->id }}, $event.target.value)"
                                        class="w-16 text-center border-0 focus:ring-0">
                                    <button wire:click="increment({{ $item->product->id }}, {{ $item->quantity }})"
                                        class="px-3 py-1.5 text-lg hover:bg-gray-50" aria-label="Agregar uno">+</button>
                                </div>

                                <div class="text-right">
                                    <p class="font-bold text-violet-900">${{ number_format($item->total_mxn, 2) }} MXN
                                    </p>
                                    <p class="text-xs text-gray-400">${{ number_format($item->total_usd, 2) }} USD</p>
                                </div>
                            </div>

                            @error("item.{$item->product->id}")
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endforeach

                <button wire:click="clear" wire:confirm="¿Vaciar el carrito?"
                    class="text-sm text-gray-500 hover:text-red-600">Vaciar carrito</button>
            </div>

            <aside class="p-6 space-y-4 bg-white shadow-sm h-fit rounded-2xl lg:sticky lg:top-24">
                <h2 class="font-semibold text-gray-900">Resumen</h2>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Subtotal ({{ $items->sum('quantity') }} piezas)</span>
                    <span class="font-semibold">${{ number_format($subtotalMxn, 2) }} MXN</span>
                </div>
                <div class="flex justify-between text-xs text-gray-400">
                    <span>Equivalente</span>
                    <span>${{ number_format($subtotalUsd, 2) }} USD</span>
                </div>
                <p class="text-xs text-gray-400">El IVA y el total se calculan en el siguiente paso.</p>

                <a href="{{ route('home') }}" wire:navigate
                    class="block text-sm font-medium text-center text-violet-700 hover:text-violet-900">
                    ← Seguir comprando
                </a>

                <a href="{{ route('checkout') }}" wire:navigate
                    class="block w-full px-6 py-3 font-semibold text-center text-white rounded-xl bg-violet-700 hover:bg-violet-800">
                    Continuar al pago
                </a>
            </aside>
        </div>
    @endif
</div>
