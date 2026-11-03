<?php

namespace App\Livewire\Admin;

use App\Models\ExchangeRate;
use App\Models\Order;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        $orders = Order::where('status', 'paid')
            ->where('ordered_at', '>=', now()->subDays(30));
        $products = Product::where('is_active', true);

        $sales = (float) (clone $orders)->sum('total');
        $ordersCount = (clone $orders)->count();
        $activeProducts = (clone $products)->count();
        $lowStock = (clone $products)->where('stock', '<=', 5)->count();
        $exchangeRate = ExchangeRate::latest('date')->first();

        return view('livewire.admin.dashboard', compact('sales','ordersCount','activeProducts','lowStock','exchangeRate'));
    }
}
