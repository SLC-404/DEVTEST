<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\ExchangeRateService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateExchangeRate extends Command
{
    protected $signature = 'exchange-rate:update {currency=USD : Moneda a consultar}';

    protected $description = 'Consulta el tipo de cambio en Banxico, lo guarda y recalcula el precio en MXN de los productos';

    public function handle(ExchangeRateService $service): int
    {
        $currency = strtoupper($this->argument('currency'));
        try {
            $exchangeRate = DB::transaction(function () use ($service, $currency) {
                $exchangeRate = $service->update($currency);

                if ($currency === 'USD') {
                    $rate = (float) $exchangeRate->rate;

                    Product::withTrashed()->update([
                        'price_mxn' => DB::raw("ROUND(price_usd * {$rate}, 2)"),
                    ]);
                }
                return $exchangeRate;
            });
        } catch (Throwable $e) {
            Cache::forget("exchange_rate:{$currency}:MXN");

            Log::error('No se pudo actualizar el tipo de cambio', [
                'currency' => $currency,
                'error'    => $e->getMessage(),
            ]);

            $this->error("No se pudo actualizar el tipo de cambio: {$e->getMessage()}");

            return self::FAILURE;
        }

        $this->info("Tipo de cambio {$currency}/MXN del {$exchangeRate->date->format('d/m/Y')}: {$exchangeRate->rate}");

        return self::SUCCESS;
    }
}
