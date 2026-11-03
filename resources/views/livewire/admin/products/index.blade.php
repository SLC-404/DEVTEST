<div class="space-y-4">
    @if (session('success'))
        <div class="rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    {{-- Filtros --}}
    <div class="flex flex-col gap-3 rounded-xl bg-white p-4 shadow-sm md:flex-row md:items-center">
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre o SKU…"
               class="w-full rounded-lg border-gray-300 text-sm focus:border-violet-500 focus:ring-violet-500 md:max-w-xs">

        <select wire:model.live="category" class="rounded-lg border-gray-300 text-sm focus:border-violet-500 focus:ring-violet-500">
            <option value="">Todas las categorías</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>

        <select wire:model.live="status" class="rounded-lg border-gray-300 text-sm focus:border-violet-500 focus:ring-violet-500">
            <option value="">Todos los estados</option>
            <option value="active">Activos</option>
            <option value="inactive">Inactivos</option>
        </select>

        <span wire:loading class="text-xs text-gray-400">Buscando…</span>

        @can('products.create')
            <a href="{{ route('products.create') }}" wire:navigate
               class="rounded-lg bg-violet-700 px-4 py-2 text-center text-sm font-medium text-white hover:bg-violet-800 md:ml-auto">
                + Nuevo producto
            </a>
        @endcan
    </div>

    {{-- Tabla --}}
    <div class="overflow-hidden rounded-xl bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Producto</th>
                        <th class="px-4 py-3 text-right">Precio</th>
                        <th class="px-4 py-3 text-center">Stock</th>
                        <th class="px-4 py-3">Vigencia</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($products as $product)
                        <tr wire:key="product-{{ $product->id }}" class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-10 w-10 rounded-lg object-cover">
                                    <div>
                                        <p class="font-medium">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $product->sku }} · {{ $product->category->name }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-right">
                                <p class="font-medium">${{ number_format((float) $product->price_mxn, 2) }} MXN</p>
                                <p class="text-xs text-gray-400">${{ number_format((float) $product->price_usd, 2) }} USD</p>
                            </td>

                            <td class="px-4 py-3 text-center">
                                <span @class([
                                    'rounded-full px-2 py-0.5 text-xs font-medium',
                                    'bg-red-100 text-red-700'       => $product->stock === 0,
                                    'bg-orange-100 text-orange-700' => $product->stock > 0 && $product->stock <= 5,
                                    'bg-gray-100 text-gray-700'     => $product->stock > 5,
                                ])>{{ $product->stock }}</span>
                            </td>

                            <td @class(['px-4 py-3', 'text-red-600' => $product->valid_until->isPast()])>
                                {{ $product->valid_until->format('d/m/Y') }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                @can('products.active')
                                    <button wire:click="toggleActive({{ $product->id }})"
                                            @class([
                                                'rounded-full px-3 py-1 text-xs font-medium transition',
                                                'bg-green-100 text-green-700 hover:bg-green-200' => $product->is_active,
                                                'bg-gray-200 text-gray-600 hover:bg-gray-300'    => ! $product->is_active,
                                            ])>
                                        {{ $product->is_active ? 'Activo' : 'Inactivo' }}
                                    </button>
                                @else
                                    <span class="text-xs">{{ $product->is_active ? 'Activo' : 'Inactivo' }}</span>
                                @endcan
                            </td>

                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-3">
                                    @can('products.edit')
                                        <a href="{{ route('products.edit', $product) }}" wire:navigate
                                           class="text-sm font-medium text-violet-700 hover:text-violet-900">Editar</a>
                                    @endcan
                                    @can('products.delete')
                                        <button wire:click="delete({{ $product->id }})"
                                                wire:confirm="¿Eliminar el producto {{ $product->sku }}?"
                                                class="text-sm font-medium text-red-600 hover:text-red-800">Eliminar</button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-400">No se encontraron productos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t px-4 py-3">
            {{ $products->links() }}
        </div>
    </div>
</div>
