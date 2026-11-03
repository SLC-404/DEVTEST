<?php

namespace App\Livewire\Store;

use App\Services\CartService;
use InvalidArgumentException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.store')]
#[Title('Carrito')]
class Cart extends Component
{
    public function increment(CartService $cart, int $productId, int $current): void
    {
        $this->changeQuantity($cart, $productId, $current + 1);
    }

    public function decrement(CartService $cart, int $productId, int $current): void
    {
        $this->changeQuantity($cart, $productId, $current - 1);
    }

    public function setQuantity(CartService $cart, int $productId, $quantity): void
    {
        $this->changeQuantity($cart, $productId, (int) $quantity);
    }

    public function remove(CartService $cart, int $productId): void
    {
        $cart->remove($productId);
        $this->dispatch('cart-updated');
    }

    public function clear(CartService $cart): void
    {
        $cart->clear();
        $this->dispatch('cart-updated');
    }

    private function changeQuantity(CartService $cart, int $productId, int $quantity): void
    {
        $this->resetErrorBag();

        try {
            $cart->update($productId, $quantity);
        } catch (InvalidArgumentException $e) {
            $this->addError("item.{$productId}", $e->getMessage());
            return;
        }

        $this->dispatch('cart-updated');
    }

    public function render(CartService $cart)
    {
        $items = $cart->items();

        return view('livewire.store.cart', [
            'items'       => $items,
            'subtotalMxn' => round($items->sum('total_mxn'), 2),
            'subtotalUsd' => round($items->sum('total_usd'), 2),
        ]);
    }
}
