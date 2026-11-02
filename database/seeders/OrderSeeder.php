<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ExchangeRate;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $rate     = (float) (ExchangeRate::latest('date')->value('rate') ?? 18.50);
        $products = Product::where('is_active', true)->get();
        $clients  = Client::factory()->count(15)->create()->load('user');

        foreach (range(1, 60) as $i) {
            $date   = fake()->dateTimeBetween('-90 days', 'now');
            $client = $clients->random();
            $items  = $products->random(fake()->numberBetween(1, 4));

            DB::transaction(function () use ($i, $date, $rate, $client, $items) {
                $order = Order::create([
                    'folio'             => 'PED-' . $date->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'client_id'         => $client->id,
                    'email'             => $client->user->email,
                    'subtotal'          => 0,
                    'taxes'             => 0,
                    'total'             => 0,
                    'exchange_rate'     => $rate,
                    'status'            => fake()->randomElement(['paid', 'paid', 'paid', 'paid', 'rejected', 'pending']),
                    'payment_reference' => 'SIM-' . strtoupper(Str::random(10)),
                    'ordered_at'        => $date,
                ]);

                $subtotal = 0;

                foreach ($items as $product) {
                    $quantity = fake()->numberBetween(1, 3);
                    $priceMxn = round((float) $product->price_usd * $rate, 2);

                    $order->details()->create([
                        'product_id' => $product->id,
                        'quantity'   => $quantity,
                        'cost_usd'   => $product->cost_usd,
                        'price_usd'  => $product->price_usd,
                        'price_mxn'  => $priceMxn,
                    ]);

                    $subtotal += $priceMxn * $quantity;
                }

                $taxes = round($subtotal * 0.16, 2);

                $order->update([
                    'subtotal' => round($subtotal, 2),
                    'taxes'    => $taxes,
                    'total'    => round($subtotal + $taxes, 2),
                ]);
            });
        }
    }
}
