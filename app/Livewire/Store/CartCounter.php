<?php

namespace App\Livewire\Store;

use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class CartCounter extends Component
{
    #[On('cart-updated')]
    public function refresh(): void
    {
    }
    public function render(CartService $cart)
    {
        return view('livewire.store.cart-counter', [
            'count' => $cart->count(),
        ]);
    }
}
