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
                'text-green-700'  => $product->stock > 5,
                'text-orange-600' => $product->stock > 0 && $product->stock <= 5,
                'text-red-600'    => $product->stock === 0,
            ])>
                @if ($product->stock === 0)
                    Agotado
                @elseif ($product->stock <= 5)
                    ¡Últimas {{ $product->stock }} piezas!
                @else
                    Disponible ({{ $product->stock }} en existencia)
                @endif
            </p>

            {{-- El botón se conecta al carrito en la siguiente feature --}}
            <button type="button" disabled
                    class="px-6 py-3 font-semibold text-white rounded-xl bg-violet-700 disabled:cursor-not-allowed disabled:opacity-50">
                Añadir al carrito
            </button>

            <div class="pt-4 border-t">
                <h2 class="mb-2 font-semibold text-gray-900">Descripción</h2>
                <div class="text-sm leading-relaxed text-gray-600 whitespace-pre-line">{{ $product->long_description }}</div>
            </div>
        </div>
    </div>
</div>
