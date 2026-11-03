<?php

namespace App\Livewire\Store;

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.store')]
#[Title('Mis pedidos')]
class MyOrders extends Component
{

    use WithPagination;
    public function render()
    {
        $orders = Order::query()
            ->whereHas('client', fn($query) => $query->where('user_id', auth()->id()))
            ->withCount('details')
            ->latest('ordered_at')
            ->paginate(10);
        return view('livewire.store.my-orders', compact('orders'));
    }
}
