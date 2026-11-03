<?php

namespace App\Livewire\Store;

use App\Models\Order;
use App\Services\OrderService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.store')]
#[Title('¡Gracias por tu compra!')]
class OrderConfirmation extends Component
{
    public Order $order;

    public function mount(Order $order): void
    {
        // Solo el dueño del pedido puede verlo
        abort_unless($order->client?->user_id === auth()->id(), 404);

        $this->order = $order->load('details.product');
    }
    public function render()
    {
        return view('livewire.store.order-confirmation', [
            'totals' => OrderService::totals($this->order->details),
        ]);
    }
}
