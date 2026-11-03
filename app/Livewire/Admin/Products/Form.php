<?php

namespace App\Livewire\Admin\Products;

use App\Livewire\Forms\ProductForm;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
class Form extends Component
{
    use WithFileUploads;

    public ProductForm $form;

    public function mount(?Product $product = null): void
    {
        if ($product?->exists) {
            $this->form->setProduct($product);
        }
    }
    public function updated(string $property): void
    {
        if (str_starts_with($property, 'form.')) {
            $this->validateOnly($property);
        }
    }

    public function save()
    {
        $this->authorize($this->form->product ? 'products.edit' : 'products.create');
        $product = $this->form->save();
        session()->flash('success', "Producto {$product->sku} guardado correctamente.");
        return $this->redirectRoute('products.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.products.form', [
            'categories' => Category::orderBy('name')->get(),
            'priceMxn'   => $this->form->priceMxn(),
        ])->title($this->form->product ? 'Editar producto' : 'Nuevo producto');
    }
}
