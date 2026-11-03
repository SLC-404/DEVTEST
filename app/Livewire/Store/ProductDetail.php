<?php

namespace App\Livewire\Store;

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Services\CartService;
use InvalidArgumentException;

#[Layout('layouts.store')]
class ProductDetail extends Component
{
    public $quantity = 1;
    public Product $product;

    public function mount(Product $product): void
    {
        abort_unless(
            Product::query()->available()->whereKey($product->id)->exists(),
            404
        );

        $this->product = $product->load('category');
    }

    public function addToCart(CartService $cart): void
    {
        $this->resetErrorBag();
        try {
            $cart->add($this->product, max(1, (int) $this->quantity));
        } catch (InvalidArgumentException $e) {
            $this->addError('quantity', $e->getMessage());
            return;
        }

        $this->quantity = 1;
        $this->dispatch('cart-updated');
        session()->flash('cart-success', 'Producto agregado al carrito 🛒');
    }

    public function render()
    {
        return view('livewire.store.product-detail')->title($this->product->name);
    }
}
