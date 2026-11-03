<?php

namespace App\Livewire\Store;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.store')]
class ProductDetail extends Component
{
    public Product $product;

    public function mount(Product $product): void
    {
        abort_unless(
            Product::query()->available()->whereKey($product->id)->exists(),
            404
        );

        $this->product = $product->load('category');
    }

    public function render()
    {
        return view('livewire.store.product-detail')->title($this->product->name);
    }
}
