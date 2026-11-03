<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Pedidos')]
class Index extends Component
{

    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $status = '';

    #[Url]
    public string $from = '';

    #[Url]
    public string $to = '';

    public function mount(): void
    {
        $this->from = $this->from ?: now()->subDays(30)->toDateString();
        $this->to   = $this->to ?: now()->toDateString();
    }

    public function updated(): void
    {
        $this->resetPage();
    }
    public function render()
    {
        $orders = Order::query()
            ->with('details.product')
            ->when($this->from, fn($q) => $q->where('ordered_at', '>=', $this->from . ' 00:00:00'))
            ->when($this->to, fn($q) => $q->where('ordered_at', '<=', $this->to . ' 23:59:59'))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->search, fn($q) => $q->where(fn($q) => $q
                ->where('folio', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")))
            ->latest('ordered_at')
            ->paginate(15);

        return view('livewire.admin.orders.index', compact('orders'));
    }
}
