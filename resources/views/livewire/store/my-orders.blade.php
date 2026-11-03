<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">Mis pedidos</h1>

    @forelse ($orders as $order)
        <a href="{{ route('order.confirmation', $order) }}" wire:navigate wire:key="my-order-{{ $order->id }}"
           class="flex flex-wrap items-center justify-between gap-4 p-5 transition bg-white shadow-sm rounded-2xl hover:shadow-md">
            <div>
                <p class="font-semibold text-violet-900">{{ $order->folio }}</p>
                <p class="text-sm text-gray-500">{{ $order->ordered_at->format('d/m/Y H:i') }} · {{ $order->details_count }} productos</p>
            </div>
            <div class="text-right">
                <p class="text-lg font-bold">${{ number_format((float) $order->total, 2) }} MXN</p>
                <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">
                    {{ $order->status === 'paid' ? 'Pagado' : ucfirst($order->status) }}
                </span>
            </div>
        </a>
    @empty
        <div class="p-10 text-center bg-white shadow-sm rounded-2xl">
            <p class="text-gray-500">Todavía no tienes pedidos.</p>
            <a href="{{ route('home') }}" wire:navigate
               class="mt-4 inline-block rounded-xl bg-violet-700 px-5 py-2.5 font-semibold text-white hover:bg-violet-800">Ver productos</a>
        </div>
    @endforelse

    {{ $orders->links() }}
</div>
