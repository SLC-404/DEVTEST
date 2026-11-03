<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class CartService
{
    private const KEY = 'cart';

    private function read(): array
    {
        return session(self::KEY, []);
    }

    private function save(array $cart): void
    {
        session([self::KEY => $cart]);
    }

    public function add(Product $product, int $quantity = 1): void
    {
        $cart    = $this->read();
        $currentCart = $cart[$product->id] ?? 0;
        $this->validateStock($product, $currentCart + $quantity);
        $cart[$product->id] = $currentCart + $quantity;
        $this->save($cart);
    }

    public function update(int $productId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->remove($productId);
            return;
        }

        $product = Product::query()->available()->findOrFail($productId);
        $this->validateStock($product, $quantity);

        $cart = $this->read();
        $cart[$productId] = $quantity;
        $this->save($cart);
    }

    public function remove(int $productId): void
    {
        $cart = $this->read();
        unset($cart[$productId]);
        $this->save($cart);
    }

    public function clear(): void
    {
        session()->forget(self::KEY);
    }

    public function count(): int
    {
        return array_sum($this->read());
    }

    public function items(): Collection
    {
        $cart = $this->read();

        if (empty($cart)) {
            return collect();
        }

        $products = Product::query()->available()
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        $items   = collect();
        $cleaned = [];

        foreach ($cart as $productId => $quantity) {
            $product = $products->get($productId);

            if (! $product || $product->stock <= 0) {
                continue;
            }

            $quantity = min($quantity, $product->stock);
            $cleaned[$productId] = $quantity;

            $items->push((object) [
                'product'   => $product,
                'quantity'  => $quantity,
                'total_mxn' => round((float) $product->price_mxn * $quantity, 2),
                'total_usd' => round((float) $product->price_usd * $quantity, 2),
            ]);
        }

        if ($cleaned !== $cart) {
            $this->save($cleaned);
        }

        return $items;
    }

    private function validateStock(Product $product, int $quantity): void
    {
        if ($quantity > $product->stock) {
            throw new InvalidArgumentException(
                "Solo hay {$product->stock} piezas disponibles de {$product->name}."
            );
        }
    }
}
