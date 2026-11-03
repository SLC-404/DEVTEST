<?php

namespace App\Services;

use App\Models\Client;
use App\Models\KardexInventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\Quote;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class OrderService
{
    public const TAX_RATE = 0.16;

    public function __construct(private ExchangeRateService $exchangeRates)
    {
    }
    public static function totals(Collection $lines): array
    {
        $subtotalMxn = round($lines->sum(fn ($l) => (float) $l->price_mxn * $l->quantity), 2);
        $subtotalUsd = round($lines->sum(fn ($l) => (float) $l->price_usd * $l->quantity), 2);
        $taxesMxn = round($subtotalMxn * self::TAX_RATE, 2);
        $taxesUsd = round($subtotalUsd * self::TAX_RATE, 2);
        return [
            'subtotal_mxn' => $subtotalMxn,
            'taxes_mxn'    => $taxesMxn,
            'total_mxn'    => round($subtotalMxn + $taxesMxn, 2),
            'subtotal_usd' => $subtotalUsd,
            'taxes_usd'    => $taxesUsd,
            'total_usd'    => round($subtotalUsd + $taxesUsd, 2),
        ];
    }
    public function syncQuote(Client $client, Collection $cartItems): Quote
    {
        return DB::transaction(function () use ($client, $cartItems) {
            $lines = $cartItems->map(fn ($item) => (object) [
                'product_id' => $item->product->id,
                'quantity'   => $item->quantity,
                'cost_usd'   => $item->product->cost_usd,
                'price_usd'  => $item->product->price_usd,
                'price_mxn'  => $item->product->price_mxn,
            ]);
            $totals = self::totals($lines);
            $quote = $client->quotes()->open()->latest('id')->first() ?? new Quote(['client_id' => $client->id]);
            $quote->fill([
                'subtotal'      => $totals['subtotal_mxn'],
                'taxes'         => $totals['taxes_mxn'],
                'total'         => $totals['total_mxn'],
                'exchange_rate' => $this->exchangeRates->current(),
            ])->save();
            $quote->items()->delete();
            $quote->items()->createMany($lines->map(fn ($l) => (array) $l)->all());
            return $quote->load('items.product');
        });
    }
    public function placeOrder(Quote $quote, string $email, string $paymentReference): Order
    {
        return DB::transaction(function () use ($quote, $email, $paymentReference) {
            $quote->load('items');
            $products = Product::whereIn('id', $quote->items->pluck('product_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');
            foreach ($quote->items as $item) {
                $product = $products->get($item->product_id);
                if (! $product || ! $product->is_active || $product->valid_until->lt(today()) || $product->stock < $item->quantity) {
                    throw new RuntimeException(
                        'Ya no hay existencia suficiente de ' . ($product?->name ?? 'un producto') . '. Revisa tu carrito.'
                    );
                }
            }
            $order = Order::create([
                'folio'             => 'TMP-' . Str::random(16),
                'client_id'         => $quote->client_id,
                'email'             => $email,
                'subtotal'          => $quote->subtotal,
                'taxes'             => $quote->taxes,
                'total'             => $quote->total,
                'exchange_rate'     => $quote->exchange_rate,
                'status'            => 'paid',
                'payment_reference' => $paymentReference,
                'ordered_at'        => now(),
            ]);
            $order->update(['folio' => sprintf('PED-%s-%06d', now()->format('Ymd'), $order->id)]);
            foreach ($quote->items as $item) {
                $product = $products->get($item->product_id);
                $order->details()->create([
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'cost_usd'   => $item->cost_usd,
                    'price_usd'  => $item->price_usd,
                    'price_mxn'  => $item->price_mxn,
                ]);
                Product::whereKey($product->id)->decrement('stock', $item->quantity);
                KardexInventory::create([
                    'product_id'   => $product->id,
                    'user_id'      => Auth::id(),
                    'order_id'     => $order->id,
                    'type'         => 'out',
                    'quantity'     => $item->quantity,
                    'stock_before' => $product->stock,
                    'stock_after'  => $product->stock - $item->quantity,
                    'notes'        => "Venta {$order->folio}",
                ]);
            }
            $quote->update(['order_id' => $order->id]);
            return $order;
        });
    }
}
