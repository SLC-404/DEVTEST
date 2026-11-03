<div class="space-y-6">
    <a href="{{ route('home') }}" wire:navigate class="text-sm font-medium text-violet-700 hover:text-violet-900">
        ← Volver al catálogo
    </a>

    <div class="grid gap-8 p-6 bg-white shadow-sm rounded-2xl md:grid-cols-2">
        <div class="overflow-hidden bg-gray-100 aspect-square rounded-xl">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="object-cover w-full h-full">
        </div>

        <div class="flex flex-col gap-4">
            <div>
                <p class="text-sm text-gray-400">{{ $product->category->name }} · SKU {{ $product->sku }}</p>
                <h1 class="mt-1 text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
                <p class="mt-2 text-gray-600">{{ $product->short_description }}</p>
            </div>

            <div class="p-4 rounded-xl bg-violet-50">
                <p class="text-3xl font-bold text-violet-900">
                    ${{ number_format((float) $product->price_mxn, 2) }}
                    <span class="text-base font-medium text-violet-400">MXN</span>
                </p>
                <p class="text-sm text-gray-500">${{ number_format((float) $product->price_usd, 2) }} USD</p>
            </div>

            <p @class([
                'text-sm font-medium',
                'text-green-700' => $product->stock > 5,
                'text-orange-600' => $product->stock > 0 && $product->stock <= 5,
                'text-red-600' => $product->stock === 0,
            ])>
                @if ($product->stock === 0)
                    Agotado
                @elseif ($product->stock <= 5)
                    ¡Últimas {{ $product->stock }} piezas!
                @else
                    Disponible ({{ $product->stock }} en existencia)
                @endif
            </p>

            @if (session('cart-success'))
                <div class="px-4 py-2 text-sm text-green-700 rounded-lg bg-green-50">
                    {{ session('cart-success') }}
                    <a href="{{ route('cart') }}" wire:navigate class="font-semibold underline">Ver carrito</a>
                </div>
            @endif

            @if ($product->stock > 0)
                <form wire:submit="addToCart" class="flex items-start gap-3">
                    <div>
                        <input type="number" min="1" max="{{ $product->stock }}" wire:model="quantity"
                            class="w-24 text-center border-gray-300 rounded-xl focus:border-violet-500 focus:ring-violet-500">
                        @error('quantity')
                            <p class="max-w-xs mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" wire:loading.attr="disabled" wire:target="addToCart"
                        class="flex-1 px-6 py-3 font-semibold text-white rounded-xl bg-violet-700 hover:bg-violet-800 disabled:opacity-50">
                        <span wire:loading.remove wire:target="addToCart">Añadir al carrito</span>
                        <span wire:loading wire:target="addToCart">Agregando…</span>
                    </button>
                </form>
            @else
                <button type="button" disabled class="px-6 py-3 font-semibold text-gray-500 bg-gray-200 rounded-xl">
                    Agotado
                </button>
            @endif
            <div class="pt-4 border-t">
                <h2 class="mb-2 font-semibold text-gray-900">Descripción</h2>
                <div class="text-sm leading-relaxed text-gray-600 whitespace-pre-line">{{ $product->long_description }}
                </div>
            </div>
        </div>
    </div>
</div>
