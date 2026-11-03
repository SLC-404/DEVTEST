<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ExchangeRate;


class ExchangeRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExchangeRate::updateOrCreate(
            [
                'date'           => '2026-10-30',
                'base_currency'  => 'USD',
                'quote_currency' => 'MXN',
            ],
            [
                'rate'   => 18.7962,
                'source' => 'banxico',
            ]
        );
    }
}
