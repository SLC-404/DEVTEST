<?php

namespace App\Livewire\Forms;

use App\Models\Product;
use App\Services\ExchangeRateService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Form;

class ProductForm extends Form
{
    public ?Product $product = null;

    public $category_id = '';
    public $sku = '';
    public $name = '';
    public $short_description = '';
    public $long_description = '';
    public $cost_usd = '';
    public $price_usd = '';
    public $stock = 0;
    public $valid_until = '';
    public $is_active = true;
    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $image=null;

    protected function rules(): array
    {
        return [
            'category_id'       => ['required', 'exists:categories,id'],
            'sku'               => ['required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($this->product?->id)],
            'name'              => ['required', 'string', 'max:150', 'regex:/^[A-Za-z0-9 ]+$/'],
            'short_description' => ['required', 'string', 'max:255'],
            'long_description'  => ['required', 'string'],
            'cost_usd'          => ['required', 'numeric', 'gt:0'],
            'price_usd'         => ['required', 'numeric', 'gt:0'],
            'stock'             => ['required', 'integer', 'min:0'],
            'valid_until'       => ['required', 'date'],
            'is_active'         => ['boolean'],
            'image'             => [$this->product ? 'nullable' : 'required', 'image', 'max:2048'],
        ];
    }

    protected function messages(): array
    {
        return [
            'required'     => 'Este campo es obligatorio.',
            'sku.unique'   => 'Ya existe un producto con este SKU.',
            'name.regex'   => 'Solo letras sin acentos, números y espacios.',
            'gt'           => 'Debe ser mayor a 0.',
            'numeric'      => 'Debe ser un número.',
            'integer'      => 'Debe ser un número entero.',
            'stock.min'    => 'No puede ser negativo.',
            'image.image'  => 'El archivo debe ser una imagen.',
            'image.max'    => 'La imagen no debe pesar más de 2 MB.',
        ];
    }
    public function setProduct(Product $product): void
    {
        $this->product = $product;

        $this->fill($product->only([
            'category_id', 'sku', 'name', 'short_description', 'long_description',
            'cost_usd', 'price_usd', 'stock', 'is_active',
        ]));

        $this->valid_until = $product->valid_until?->format('Y-m-d');
    }
    public function priceMxn(): float
    {
        if (! is_numeric($this->price_usd)) {
            return 0;
        }

        return round((float) $this->price_usd * app(ExchangeRateService::class)->current(), 2);
    }

    public function save(): Product
    {
        $data = $this->validate();
        unset($data['image']);

        $data['price_mxn'] = $this->priceMxn();

        $oldImage = $this->product?->image;

        if ($this->image) {
            $data['image'] = $this->image->store('products', 'public');
        }

        $product = $this->product ?? new Product();
        $product->fill($data)->save();

        if ($this->image && $oldImage && ! Str::startsWith($oldImage, ['http://', 'https://'])) {
            Storage::disk('public')->delete($oldImage);
        }

        return $product;
    }
}
