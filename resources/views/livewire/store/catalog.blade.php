<div class="grid gap-6 lg:grid-cols-4">

    {{-- Filtros --}}
    <aside class="p-5 space-y-5 bg-white shadow-sm rounded-xl lg:sticky lg:top-24 lg:self-start">
        <div>
            <label class="block text-sm font-medium text-gray-700">Buscar</label>
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Nombre del producto…"
                   class="w-full mt-1 text-sm border-gray-300 rounded-lg focus:border-violet-500 focus:ring-violet-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Categoría</label>
            <select wire:model.live="category"
                    class="w-full mt-1 text-sm border-gray-300 rounded-lg focus:border-violet-500 focus:ring-violet-500">
                <option value="">Todas</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Precio (MXN)</label>
            <div class="grid grid-cols-2 gap-2 mt-1">
                <input type="number" min="0" wire:model.live.debounce.500ms="minPrice" placeholder="Mín."
                       class="w-full text-sm border-gray-300 rounded-lg focus:border-violet-500 focus:ring-violet-500">
                <input type="number" min="0" wire:model.live.debounce.500ms="maxPrice" placeholder="Máx."
                       class="w-full text-sm border-gray-300 rounded-lg focus:border-violet-500 focus:ring-violet-500">
            </div>
        </div>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" wire:model.live="inStock" class="border-gray-300 rounded text-violet-700 focus:ring-violet-500">
            Solo disponibles
        </label>

        <button wire:click="clearFilters" class="text-sm font-medium text-violet-700 hover:text-violet-900">
            Limpiar filtros
        </button>
    </aside>

    {{-- Productos --}}
    <section class="space-y-4 lg:col-span-3">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">{{ $products->total() }} productos</p>
            <span wire:loading class="text-xs text-gray-400">Actualizando…</span>
        </div>

        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-4" wire:loading.class="opacity-50">
            @forelse ($products as $product)
                <a href="{{ route('products.detail', $product) }}" wire:navigate wire:key="product-{{ $product->id }}"
                   class="overflow-hidden transition bg-white shadow-sm group rounded-xl hover:shadow-md">
                    <div class="relative overflow-hidden bg-gray-100 aspect-square">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy"
                             class="object-cover w-full h-full transition group-hover:scale-105">
                        @if ($product->stock === 0)
                            <span class="absolute left-3 top-3 rounded-full bg-gray-900/80 px-2 py-0.5 text-xs font-medium text-white">Agotado</span>
                        @endif
                    </div>
                    <div class="p-4 space-y-1">
                        <p class="text-xs text-gray-400">{{ $product->category->name }}</p>
                        <p class="font-semibold">{{ $product->name }}</p>
                        <p class="text-sm text-gray-500 line-clamp-2">{{ $product->short_description }}</p>
                        <p class="pt-1 text-lg font-bold text-violet-900">
                            ${{ number_format((float) $product->price_mxn, 2) }}
                            <span class="text-xs font-medium text-gray-400">MXN</span>
                        </p>
                        <p class="text-xs text-gray-400">${{ number_format((float) $product->price_usd, 2) }} USD</p>
                    </div>
                </a>
            @empty
                <div class="p-10 text-center text-gray-400 bg-white col-span-full rounded-xl">
                    No encontramos productos con esos filtros.
                </div>
            @endforelse
        </div>

        {{ $products->links() }}
    </section>
</div>
