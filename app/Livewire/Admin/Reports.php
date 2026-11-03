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
        $from = Carbon::parse($this->from)->startOfDay();
        $to   = Carbon::parse($this->to)->endOfDay();

        $base = fn() => DB::table('order_details')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->where('orders.status', 'paid')
            ->whereBetween('orders.ordered_at', [$from, $to]);

        $salesExpr  = 'SUM(order_details.price_mxn * order_details.quantity)';
        $profitExpr = 'SUM((order_details.price_mxn - order_details.cost_usd * orders.exchange_rate) * order_details.quantity)';

        $kpis = $base()
            ->selectRaw("$salesExpr as sales, $profitExpr as profit, COUNT(DISTINCT orders.id) as orders")
            ->first();

        $sqlFormat = $this->groupBy === 'month' ? '%Y-%m' : '%Y-%m-%d';

        $rows = $base()
            ->selectRaw("DATE_FORMAT(orders.ordered_at, '$sqlFormat') as period, $salesExpr as sales, $profitExpr as profit")
            ->groupBy('period')
            ->get()
            ->keyBy('period');

        $periods = $this->groupBy === 'month'
            ? CarbonPeriod::create($from->copy()->startOfMonth(), '1 month', $to)
            : CarbonPeriod::create($from, '1 day', $to);

        $chart = ['labels' => [], 'sales' => [], 'profit' => []];

        foreach ($periods as $date) {
            $key = $this->groupBy === 'month' ? $date->format('Y-m') : $date->format('Y-m-d');

            $chart['labels'][] = $this->groupBy === 'month' ? $date->translatedFormat('M Y') : $date->format('d/m');
            $chart['sales'][]  = round((float) ($rows[$key]->sales ?? 0), 2);
            $chart['profit'][] = round((float) ($rows[$key]->profit ?? 0), 2);
        }

        $topProducts = OrderDetail::query()
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->where('orders.status', 'paid')
            ->whereBetween('orders.ordered_at', [$from, $to])
            ->select('order_details.product_id')
            ->selectRaw('SUM(order_details.quantity) as units, SUM(order_details.price_mxn * order_details.quantity) as sales')
            ->groupBy('order_details.product_id')
            ->orderByDesc('units')
            ->limit(3)
            ->with('product')
            ->get();

        $this->dispatch('report-updated', ...$chart);

        return view('livewire.admin.reports', compact('kpis', 'chart', 'topProducts'));
    }
}
