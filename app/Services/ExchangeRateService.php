<?php

namespace App\Services;

use App\Models\ExchangeRate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ExchangeRateService
{
    public function current(string $currency = 'USD'): float
    {
        return (float) Cache::remember($this->cacheKey($currency), now()->endOfDay(), function () use ($currency) {
            $rate = ExchangeRate::where('base_currency', $currency)
                ->where('quote_currency', 'MXN')
                ->whereDate('date', '<=', today())
                ->latest('date')
                ->value('rate');
            return $rate ?? $this->update($currency)->rate;
        });
    }
    public function update(string $currency = 'USD'): ExchangeRate
    {
        ['date' => $date, 'rate' => $rate] = $this->fetchFromBanxico($currency);

        $exchangeRate = ExchangeRate::updateOrCreate(
            [
                'date'           => $date->toDateString(),
                'base_currency'  => $currency,
                'quote_currency' => 'MXN',
            ],
            [
                'rate'   => $rate,
                'source' => 'banxico',
            ]
        );

        Cache::put($this->cacheKey($currency), $exchangeRate->rate, now()->endOfDay());

        return $exchangeRate;
    }
    private function fetchFromBanxico(string $currency): array
    {
        $series = config("services.banxico.series.{$currency}")
            ?? throw new RuntimeException("No hay serie de Banxico configurada para {$currency}.");

        $response = Http::withHeaders(['Bmx-Token' => config('services.banxico.token')])
            ->acceptJson()
            ->timeout(10)
            ->retry(3, 500)
            ->get(config('services.banxico.url') . "/{$series}/datos/oportuno");

        $data  = $response->json('bmx.series.0.datos.0');
        $value = str_replace(',', '', $data['dato'] ?? '');

        if (! is_numeric($value)) {
            throw new RuntimeException('Banxico respondió sin un tipo de cambio válido.');
        }

        return [
            'date' => Carbon::createFromFormat('d/m/Y', $data['fecha'])->startOfDay(),
            'rate' => (float) $value,
        ];
    }

    private function cacheKey(string $currency): string
    {
        return "exchange_rate:{$currency}:MXN";
    }
}
