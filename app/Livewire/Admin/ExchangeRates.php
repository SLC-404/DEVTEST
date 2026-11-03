<?php

namespace App\Livewire\Admin;

use App\Models\ExchangeRate;
use App\Services\ExchangeRateService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Tipo de cambio')]
class ExchangeRates extends Component
{
    use WithPagination;

    public function render()
    {
        $current = app(ExchangeRateService::class)->current();

        $rates = ExchangeRate::where('base_currency', 'USD')
            ->where('quote_currency', 'MXN')
            ->orderByDesc('date')
            ->paginate(15);

        return view('livewire.admin.exchange-rates', compact('current', 'rates'));
    }
}
