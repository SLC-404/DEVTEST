<div class="space-y-6">
    <div class="rounded-xl bg-white p-5 shadow-sm">
        <p class="text-sm text-gray-500">Tipo de cambio vigente (DOF)</p>
        <p class="mt-2 text-3xl font-bold">
            ${{ number_format($current, 4) }}
            <span class="text-sm font-medium text-gray-400">MXN por USD</span>
        </p>
        <p class="mt-1 text-xs text-gray-400">Se actualiza automáticamente desde Banxico todos los días a las 00:01.</p>
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-5 py-3">Fecha</th>
                        <th class="px-5 py-3">Moneda</th>
                        <th class="px-5 py-3 text-right">Tipo de cambio</th>
                        <th class="px-5 py-3">Fuente</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($rates as $rate)
                        <tr wire:key="rate-{{ $rate->id }}" class="hover:bg-gray-50">
                            <td class="px-5 py-3">{{ $rate->date->format('d/m/Y') }}</td>
                            <td class="px-5 py-3">{{ $rate->base_currency }}/{{ $rate->quote_currency }}</td>
                            <td class="px-5 py-3 text-right font-medium">${{ number_format((float) $rate->rate, 4) }}</td>
                            <td class="px-5 py-3">
                                <span class="rounded-full bg-violet-100 px-2 py-0.5 text-xs font-medium text-violet-700">
                                    {{ ucfirst($rate->source) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-gray-400">Aún no hay tipos de cambio registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t px-5 py-3">
            {{ $rates->links() }}
        </div>
    </div>
</div>
