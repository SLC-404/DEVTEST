@php
    $input = 'mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-violet-500 focus:ring-violet-500';
    $label = 'block text-sm font-medium text-gray-700';
    $error = 'mt-1 text-xs text-red-600';
@endphp

<form wire:submit="save" class="space-y-6">
    <div class="grid gap-6 lg:grid-cols-3">

        <div class="p-6 space-y-4 bg-white shadow-sm rounded-xl lg:col-span-2">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="{{ $label }}">SKU</label>
                    <input type="text" wire:model.blur="form.sku" class="{{ $input }}">
                    @error('form.sku')
                        <p class="{{ $error }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $label }}">Categoría</label>
                    <select wire:model.blur="form.category_id" class="{{ $input }}">
                        <option value="">Selecciona…</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('form.category_id')
                        <p class="{{ $error }}">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="{{ $label }}">Nombre</label>
                <input type="text" wire:model.blur="form.name" class="{{ $input }}">
                <p class="mt-1 text-xs text-gray-400">Sin acentos ni caracteres especiales.</p>
                @error('form.name')
                    <p class="{{ $error }}">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="{{ $label }}">Descripción corta</label>
                <input type="text" wire:model.blur="form.short_description" class="{{ $input }}">
                @error('form.short_description')
                    <p class="{{ $error }}">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="{{ $label }}">Descripción larga</label>
                <textarea rows="5" wire:model.blur="form.long_description" class="{{ $input }}"></textarea>
                @error('form.long_description')
                    <p class="{{ $error }}">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="p-6 space-y-3 bg-white shadow-sm rounded-xl">
            <label class="{{ $label }}">Imagen</label>

            <div class="overflow-hidden bg-gray-100 rounded-lg aspect-square">
                @if ($form->image)
                    <img src="{{ $form->image->temporaryUrl() }}" class="object-cover w-full h-full">
                @elseif ($form->product)
                    <img src="{{ $form->product->image_url }}" class="object-cover w-full h-full">
                @else
                    <div class="grid h-full text-sm text-gray-400 place-items-center">Sin imagen</div>
                @endif
            </div>

            <input type="file" wire:model="form.image" accept="image/*" class="block w-full text-sm">
            <p wire:loading wire:target="form.image" class="text-xs text-gray-400">Subiendo imagen…</p>
            @error('form.image')
                <p class="{{ $error }}">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid gap-4 p-6 bg-white shadow-sm rounded-xl sm:grid-cols-2 lg:grid-cols-5">
        <div>
            <label class="{{ $label }}">Costo (USD)</label>
            <input type="number" step="0.01" wire:model.blur="form.cost_usd" class="{{ $input }}">
            @error('form.cost_usd')
                <p class="{{ $error }}">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="{{ $label }}">Precio (USD)</label>
            <input type="number" step="0.01" wire:model.live.debounce.400ms="form.price_usd"
                class="{{ $input }}">
            @error('form.price_usd')
                <p class="{{ $error }}">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="{{ $label }}">Precio (MXN)</label>
            <p class="px-3 py-2 mt-1 text-sm font-semibold rounded-lg bg-gray-50">${{ number_format($priceMxn, 2) }}
            </p>
            <p class="mt-1 text-xs text-gray-400">Calculado con el tipo de cambio vigente.</p>
        </div>

        <div>
            <label class="{{ $label }}">Stock</label>
            <input type="number" min="0" wire:model.blur="form.stock" class="{{ $input }}">
            @error('form.stock')
                <p class="{{ $error }}">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="{{ $label }}">Vigencia</label>
            <input type="date" wire:model.blur="form.valid_until" class="{{ $input }}">
            @error('form.valid_until')
                <p class="{{ $error }}">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" wire:model="form.is_active"
                class="border-gray-300 rounded text-violet-700 focus:ring-violet-500">
            Producto activo
        </label>

        <div class="flex gap-3">
            <a href="{{ route('products.index') }}" wire:navigate
                class="px-4 py-2 text-sm font-medium text-gray-700 border rounded-lg hover:bg-gray-50">Cancelar</a>
            <button type="submit" wire:loading.attr="disabled" wire:target="save"
                class="px-4 py-2 text-sm font-medium text-white rounded-lg bg-violet-700 hover:bg-violet-800 disabled:opacity-50">
                <span wire:loading.remove wire:target="save">Guardar</span>
                <span wire:loading wire:target="save">Guardando…</span>
            </button>
        </div>
    </div>
</form>
