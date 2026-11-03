<div class="space-y-4">
    @if (session('success'))
        <div class="px-4 py-3 text-sm text-green-700 rounded-lg bg-green-50">{{ session('success') }}</div>
    @endif

    {{-- Filtros --}}
    <div class="flex flex-col gap-3 p-4 bg-white shadow-sm rounded-xl md:flex-row md:items-center">
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre o SKU…"
            class="w-full text-sm border-gray-300 rounded-lg focus:border-violet-500 focus:ring-violet-500 md:max-w-xs">

        <select wire:model.live="category"
            class="text-sm border-gray-300 rounded-lg focus:border-violet-500 focus:ring-violet-500">
            <option value="">Todas las categorías</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>

        <select wire:model.live="status"
            class="text-sm border-gray-300 rounded-lg focus:border-violet-500 focus:ring-violet-500">
            <option value="">Todos los estados</option>
            <option value="active">Activos</option>
            <option value="inactive">Inactivos</option>
        </select>

        <span wire:loading class="text-xs text-gray-400">Buscando…</span>

        @can('products.create')
            <a href="{{ route('products.create') }}" wire:navigate
                class="px-4 py-2 text-sm font-medium text-center text-white rounded-lg bg-violet-700 hover:bg-violet-800 md:ml-auto">
                + Nuevo producto
            </a>
        @endcan
    </div>

    {{-- Tabla --}}
    <div class="overflow-hidden bg-white shadow-sm rounded-xl">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-xs tracking-wide text-left text-gray-500 uppercase bg-gray-50">
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
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                        class="object-cover w-10 h-10 rounded-lg">
                                    <div>
                                        <p class="font-medium">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $product->sku }} ·
                                            {{ $product->category->name }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-right">
                                <p class="font-medium">${{ number_format((float) $product->price_mxn, 2) }} MXN</p>
                                <p class="text-xs text-gray-400">${{ number_format((float) $product->price_usd, 2) }}
                                    USD</p>
                            </td>

                            <td class="px-4 py-3 text-center">
                                <span @class([
                                    'rounded-full px-2 py-0.5 text-xs font-medium',
                                    'bg-red-100 text-red-700' => $product->stock === 0,
                                    'bg-orange-100 text-orange-700' =>
                                        $product->stock > 0 && $product->stock <= 5,
                                    'bg-gray-100 text-gray-700' => $product->stock > 5,
                                ])>{{ $product->stock }}</span>
                            </td>

                            <td @class([
                                'px-4 py-3',
                                'text-red-600' => $product->valid_until->isPast(),
                            ])>
                                {{ $product->valid_until->format('d/m/Y') }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                @can('products.active')
                                    <button wire:click="toggleActive({{ $product->id }})" @class([
                                        'rounded-full px-3 py-1 text-xs font-medium transition',
                                        'bg-green-100 text-green-700 hover:bg-green-200' => $product->is_active,
                                        'bg-gray-200 text-gray-600 hover:bg-gray-300' => !$product->is_active,
                                    ])>
                                        {{ $product->is_active ? 'Activo' : 'Inactivo' }}
                                    </button>
                                @else
                                    <span class="text-xs">{{ $product->is_active ? 'Activo' : 'Inactivo' }}</span>
                                @endcan
                            </td>

                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-3">
                                    @can('products.show')
                                        <button wire:click="showDetail({{ $product->id }})"
                                            class="text-sm font-medium text-gray-600 hover:text-gray-900">Detalle</button>
                                    @endcan
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
                            <td colspan="6" class="px-4 py-10 text-center text-gray-400">No se encontraron productos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t">
            {{ $products->links() }}
        </div>
    </div>

    @if ($detail)
        <div class="fixed inset-0 z-50 grid p-4 place-items-center bg-black/40" wire:click.self="closeDetail">
            <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-xs text-gray-400">{{ $detail->category->name }} · SKU {{ $detail->sku }}</p>
                        <h2 class="text-xl font-bold">{{ $detail->name }}</h2>
                    </div>
                    <button wire:click="closeDetail"
                        class="text-2xl leading-none text-gray-400 hover:text-gray-700">&times;</button>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <img src="{{ $detail->image_url }}" alt="{{ $detail->name }}"
                        class="object-cover w-full aspect-square rounded-xl">

                    <dl class="grid grid-cols-2 gap-3 text-sm">
                        <dt class="text-gray-500">Precio USD</dt>
                        <dd class="font-semibold">${{ number_format((float) $detail->price_usd, 2) }}</dd>
                        <dt class="text-gray-500">Precio MXN</dt>
                        <dd class="font-semibold">${{ number_format((float) $detail->price_mxn, 2) }}</dd>
                        <dt class="text-gray-500">Costo USD</dt>
                        <dd>${{ number_format((float) $detail->cost_usd, 2) }}</dd>
                        <dt class="text-gray-500">Stock</dt>
                        <dd>{{ $detail->stock }}</dd>
                        <dt class="text-gray-500">Vigencia</dt>
                        <dd @class(['text-red-600' => $detail->valid_until->isPast()])>{{ $detail->valid_until->format('d/m/Y') }}</dd>
                        <dt class="text-gray-500">Estado</dt>
                        <dd>{{ $detail->is_active ? 'Activo' : 'Inactivo' }}</dd>
                        <dt class="text-gray-500">Creado</dt>
                        <dd>{{ $detail->created_at->format('d/m/Y H:i') }}</dd>
                        <dt class="text-gray-500">Actualizado</dt>
                        <dd>{{ $detail->updated_at->format('d/m/Y H:i') }}</dd>
                    </dl>
                </div>

                <div class="mt-6 space-y-2 text-sm">
                    <p class="font-medium text-gray-700">{{ $detail->short_description }}</p>
                    <p class="text-gray-600 whitespace-pre-line">{{ $detail->long_description }}</p>
                </div>

                @can('products.edit')
                    <div class="mt-6 text-right">
                        <a href="{{ route('products.edit', $detail) }}" wire:navigate
                            class="px-4 py-2 text-sm font-medium text-white rounded-lg bg-violet-700 hover:bg-violet-800">Editar</a>
                    </div>
                @endcan
            </div>
        </div>
    @endif

</div>
