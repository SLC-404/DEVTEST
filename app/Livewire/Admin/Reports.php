<?php

namespace App\Livewire\Admin;

use App\Models\OrderDetail;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Reportes')]
class Reports extends Component
{
    #[Url]
    public string $from = '';

    #[Url]
    public string $to = '';

    #[Url]
    public string $groupBy = 'day';

    public function mount(): void
    {
        $this->from = $this->from ?: now()->subDays(29)->toDateString();
        $this->to   = $this->to ?: now()->toDateString();
    }

    public function render()
    {
        $totals      = $this->totals();
        $chart       = $this->chart();
        $topProducts = $this->topProducts();
        $this->dispatch('report-updated', ...$chart);
        return view('livewire.admin.reports', [
            'kpis'        => $totals,
            'chart'       => $chart,
            'topProducts' => $topProducts,
        ]);
    }
    private function paidSales()
    {
        return OrderDetail::query()
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->where('orders.status', 'paid')
            ->whereDate('orders.ordered_at', '>=', $this->from)
            ->whereDate('orders.ordered_at', '<=', $this->to);
    }
    private function totals()
    {
        return $this->paidSales()
            ->selectRaw('SUM(order_details.price_mxn * order_details.quantity) as sales')
            ->selectRaw('SUM((order_details.price_mxn - order_details.cost_usd * orders.exchange_rate) * order_details.quantity) as profit')
            ->selectRaw('COUNT(DISTINCT orders.id) as orders')
            ->first();
    }
    private function chart(): array
    {
        $format = $this->groupBy === 'month' ? '%Y-%m' : '%Y-%m-%d';
        $rows = $this->paidSales()
            ->selectRaw("DATE_FORMAT(orders.ordered_at, '$format') as period")
            ->selectRaw('SUM(order_details.price_mxn * order_details.quantity) as sales')
            ->selectRaw('SUM((order_details.price_mxn - order_details.cost_usd * orders.exchange_rate) * order_details.quantity) as profit')
            ->groupBy('period')
            ->orderBy('period')
            ->get();
        return [
            'labels' => $rows->pluck('period')->all(),
            'sales'  => $rows->pluck('sales')->map(fn ($value) => round($value, 2))->all(),
            'profit' => $rows->pluck('profit')->map(fn ($value) => round($value, 2))->all(),
        ];
    }
    private function topProducts()
    {
        return $this->paidSales()
            ->select('order_details.product_id')
            ->selectRaw('SUM(order_details.quantity) as units')
            ->selectRaw('SUM(order_details.price_mxn * order_details.quantity) as sales')
            ->groupBy('order_details.product_id')
            ->orderByDesc('units')
            ->limit(3)
            ->with('product')
            ->get();
    }
}
